<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form;

use JDZ\Form\Renderer\Renderer;
use JDZ\Form\Validator\Validator;
use JDZ\Form\Field\FieldInterface;
use JDZ\Form\Exception\RequiredException;
use JDZ\Form\Exception\InvalidException;
use JDZ\Helpers\ArrayHelper;
use JDZ\Registry\Registry;
use JDZ\Utilities\DataObject;
use JDZ\Utilities\Date as DateObject;
use SimpleXMLElement;
use RuntimeException;
use Exception;

/**
 * Form
 *
 * Build, populate, filter and validate forms.
 * It uses XML definitions to construct form fields, field and rule classes to
 * render and validate the form.
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Form implements FormInterface
{
  /**
   * The boostrap3 class for label html display
   * 
   * @var    constant   
   */
  const BS_COL_LABEL='col-sm-3';
  
  /**
   * The boostrap3 class for field container html display
   * 
   * @var    constant   
   */
  const BS_COL_FIELD='col-sm-9';
  
  /**
   * The Registry data storage for form fields during display
   * 
   * @var    object   
   */
  protected $data;

  /**
   * The name of the form instance
   * 
   * @var    string 
   */
  protected $name;

  /**
   * The form object options for use in rendering and validation
   * 
   * @var    array 
   */
  protected $options;

  /**
   * Form XML definition
   * 
   * @var    SimpleXMLElement 
   */
  protected $xml;
  
  /**
   * Form Validator instance
   * 
   * @var    Validator 
   */
  protected $validator;

  /**
   * Renderer instance
   * 
   * @var    Renderer 
   */
  protected $renderer;
  
  /**
   * Current HTML indent
   * 
   * @var    string 
   */
  protected $currentIndent;
  
  /** 
   * An array of error messages or Exception objects
   * 
   * @var   array 
   */ 
  protected $errors = [];
  
  /**
   * Form instances
   * 
   * @var    array 
   */
  protected static $instances;
  
  /**
   * Adds a new child SimpleXMLElement node to the source
   * 
   * @param   SimpleXMLElement  $source  The source element on which to append.
   * @param   SimpleXMLElement  $new     The new element to append.
   * @return   void
   * @throws   RuntimeException
   */
  public static function addNode(SimpleXMLElement $source, SimpleXMLElement $new)
  {
    $node = $source->addChild($new->getName(), trim($new));

    foreach($new->attributes() as $name => $value){
      $node->addAttribute($name, $value);
    }

    foreach($new->children() as $child){
      self::addNode($node, $child);
    }
  }

  /**
   * Adds a new child SimpleXMLElement node to the source.
   * @param   SimpleXMLElement  $source  The source element on which to append.
   * @param   SimpleXMLElement  $new     The new element to append.
   * @return   void
   */
  public static function mergeNode(SimpleXMLElement $source, SimpleXMLElement $new)
  {
    foreach($new->attributes() as $name => $value){
      if ( isset($source[$name]) ){
        $source[$name] = (string) $value;
      }
      else {
        $source->addAttribute($name, $value);
      }
    }
  }

  /**
   * Merges new elements into a source <fields> element.
   * @param   SimpleXMLElement  $source  The source element.
   * @param   SimpleXMLElement  $new     The new element to merge.
   * @return   void
   */
  public static function mergeNodes(SimpleXMLElement $source, SimpleXMLElement $new)
  {
    // The assumption is that the inputs are at the same relative level.
    // So we just have to scan the children and deal with them.

    // Update the attributes of the child node.
    foreach($new->attributes() as $name => $value){
      if ( isset($source[$name]) ){
        $source[$name] = (string)$value;
      }
      else {
        $source->addAttribute($name, $value);
      }
    }

    foreach($new->children() as $child){
      $type = $child->getName();
      $name = $child['name'];

      $fields = $source->xpath($type.'[@name="'.$name.'"]');

      if ( empty($fields) ){
        // This node does not exist, so add it.
        self::addNode($source, $child);
      }
      else {
        // This node does exist.
        switch($type){
          case 'field':
            self::mergeNode($fields[0], $child);
            break;

          default:
            self::mergeNodes($fields[0], $child);
            break;
        }
      }
    }
  }
  
  /**
   * Instantiate the form object
   * 
   * @param   string            $name     The name of the form.
   * @param   SimpleXMLElement  $xml      The form xml definition
   * @param   array             $options  An array of form options.
   * @param   bool              $reset    Flag to toggle whether form fields should be replaced if a field
   *                                      already exists with the same group/name.
   * @param   string|false      $xpath    An optional xpath to search for the fields.
   */
  public function __construct($name, array $options=[])
  {
    $this->errors        = [];
    $this->options       = [];
    $this->currentIndent = '';
    
    $this->name = $name;
    $this->data = new Registry;
    
    $this->options['control']   = isset($options['control'])   ? $options['control']   : 'csForm';
    $this->options['type']      = isset($options['type'])      ? $options['type']      : 'vertical';
    $this->options['labelCols'] = isset($options['labelCols']) ? $options['labelCols'] : Form::BS_COL_LABEL;
    $this->options['fieldCols'] = isset($options['fieldCols']) ? $options['fieldCols'] : Form::BS_COL_FIELD;
    $this->options['buttons']   = isset($options['buttons'])   ? $options['buttons']   : '';
    $this->options['update']    = isset($options['update'])    ? (bool)$options['update'] : false;
  }
  
  /**
   * {@inheritDoc}
   */
  public function load(SimpleXMLElement $xml, $replace=true, $xpath=false)
  {
    if ( empty($this->xml) ){
      if ( $xpath === false && ($xml->getName() === 'form') ){
        $this->xml = $xml;
        $this->syncPaths();
        return;
      }
      
      throw new RuntimeException('Invalid XML form ['.get_class($this).']');
    }
    
    $elements = [];
    if ( $xpath === true ){
      $elements = $xml->xpath($xpath);
    }
    elseif ( $xml->getName() == 'form' ){
      $elements = $xml->children();
    }
    
    if ( empty($elements) ){
      return;
    }
    
    foreach($elements as $element){
      $fields = $element->xpath('descendant-or-self::field');
      foreach ($fields as $field){
        $attrs  = $field->xpath('ancestor::fields[@name]/@name');
        $groups = array_map('strval', $attrs ? $attrs : []);

        if ( $current = $this->findField((string) $field['name'], implode('.', $groups)) ){
          if ( $replace ){
            $olddom = dom_import_simplexml($current);
            $loadeddom = dom_import_simplexml($field);
            $addeddom = $olddom->ownerDocument->importNode($loadeddom);
            $olddom->parentNode->replaceChild($addeddom, $olddom);
            $loadeddom->parentNode->removeChild($loadeddom);
          }
          else {
            unset($field);
          }
        }
      }

      self::addNode($this->xml, $element);
    }
    
    $this->syncPaths();
  }
  
  /**
   * {@inheritDoc}
   */
  public function bind($data)
  {
    if ( !is_object($data) && !is_array($data) ){
      return false;
    }
    
    if ( is_object($data) ){
      if ( $data instanceof Registry ){
        $data = $data->toArray();
      }
      elseif ( $data instanceof DataObject ){
        $data = $data->export();
      }
      else {
        $data = (array) $data;
      }
    }
    
    foreach($data as $k => $v){
      if ( $this->findField($k) ){
        $this->data->set($k, $v);
      }
      elseif ( is_object($v) || ArrayHelper::isAssociative($v) ){
        $this->bindLevel($k, $v);
      }
    }
    
    return true;
  }
  
  /**
   * {@inheritDoc}
   */
  public function reset($xml=false)
  {
    unset($this->data);
    $this->data = new Registry;

    if ( $xml ){
      unset($this->xml);
      $this->xml = new SimpleXMLElement('<form></form>');
    }
    
    return true;
  }

  /**
   * {@inheritDoc}
   */
  public function filter($data, $group=null)
  {
    $fields = $this->findFieldsByGroup($group);
    if ( !$fields ){
      throw new RuntimeException('No fields found for '.$group);
    }
    
    $input  = new Registry($data);
    $output = new Registry;
    
    foreach($fields as $element){
      $attrs   = $element->xpath('ancestor::fields[@name]/@name');
      $groups  = array_map('strval', $attrs ? $attrs : []);
      $group   = implode('.', $groups);
      $name    = (string) $element['name'];
      
      $key = ($group===''?'':$group.'.').$name;
      
      $field = FormHelper::loadField($this, $element, $group, $input->get($key, '', 'raw'));
      $element = $field->get('element');
      $filter  = (string) $element['filter'];
      $default = (string) $element['default'];
      
      if ( empty($filter) ){
        $filter = 'string';
      }
      /* elseif ( is_callable($filter) ){
        $filter = 'raw';
      } */
      
      if ( $input->exists($key) ){
        $output->set($key, $this->filterField($field, $input->get($key, $default, $filter)));
      }
    }
    
    return $output->toArray();
  }

  /**
   * {@inheritDoc}
   */
  public function validate($data, $group=null)
  {
    return $this->getValidator()->execute($data, $group);
  }
  
  /**
   * {@inheritDoc}
   */
  public function setError(Exception $e, FieldInterface $field)
  {
    $error = [];
    $error['type']    = 'error';
    $error['field']   = $field;
    $error['message'] = $e->getMessage();
    
    if ( $e instanceof RequiredException ){
      $error['type'] = 'required';
    }
    elseif ( $e instanceof InvalidException ){
      $error['type'] = 'invalid';
    }
    
    $this->errors[] = (object)$error;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setFieldAttribute($name, $attribute, $value, $group=null)
  {
    $element = $this->findField($name, $group);
    
    if ( !($element instanceof SimpleXMLElement) ){
      throw new RuntimeException(__CLASS__ .'->'. __METHOD__ . ' Invalid SimpleXMLElement : $element'); 
    }
    
    $element[$attribute] = $value;
    
    $this->syncPaths();
    
    return true;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setFieldAttributes($name, $attributes, $group=null)
  {
    $element = $this->findField($name, $group);
    
    if ( !($element instanceof SimpleXMLElement) ){
      throw new RuntimeException(__CLASS__ .'->'. __METHOD__ . ' Invalid SimpleXMLElement : $element'); 
    }
    
    foreach($attributes as $attribute => $value){
      $element[$attribute] = $value;
    }
    
    $this->syncPaths();
    
    return true;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setValue($name, $group=null, $value=null)
  {
    if ( !$this->findField($name, $group) ){
      return false;
    }
    
    if ( $group ){
      $this->data->set($group.'.'.$name, $value);
    }
    else {
      $this->data->set($name, $value);
    }

    return true;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setFields(&$elements, $group=null, $replace=true)
  {
    foreach ($elements as $element){
      if ( !($element instanceof SimpleXMLElement) ){
        throw new RuntimeException(__CLASS__ .'->'. __METHOD__ . ' Invalid SimpleXMLElement : $element'); 
      }
    }
    
    $return = true;
    foreach($elements as $element){
      if ( !$this->setField($element, $group, $replace) ){
        $return = false;
      }
    }

    $this->syncPaths();
    return $return;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setField(&$element, $group=null, $replace=true)
  {
    if ( !($element instanceof SimpleXMLElement) ){
      throw new RuntimeException(__CLASS__ .'->'. __METHOD__ . ' Invalid SimpleXMLElement : $element'); 
    }

    $old = &$this->findField((string) $element['name'], $group);

    if ( !$replace && !empty($old) ){
      return true;
    }

    if ( $replace && !empty($old) && ($old instanceof SimpleXMLElement) ){
      $dom = dom_import_simplexml($old);
      $dom->parentNode->removeChild($dom);
    }
    
    if ( $group ){
      $fields = &$this->findGroup($group);
      if ( isset($fields[0]) && ($fields[0] instanceof SimpleXMLElement) ){
        self::addNode($fields[0], $element);
      }
    }
    else {
      self::addNode($this->xml, $element);
    }

    $this->syncPaths();
    
    return true;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFormOption($property)
  {
    if ( isset($this->options[$property]) ){
      return $this->options[$property];
    }
    
    return false;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getOption($key, $default='')
  {
    switch($key){
      case 'labelCols':
      case 'fieldCols':
        if ( $this->getOption('type') !== 'horizontal' ){
          return $default;
        }
        break;
    }
    
    return (string) $this->options[$key];
  }
  
  /**
   * {@inheritDoc}
   */
  public function getErrorsAsString($separator='<br />')
  {
    $errors = [];
    
    foreach($this->errors as &$error){
      $errors[] = $error->message;
    }
    
    return implode($separator, $errors);
  }
  
  /**
   * {@inheritDoc}
   */
  public function getErrors()
  {
    return $this->errors;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getValidator()
  {
    if ( !isset($this->validator) ){
      $this->validator = new Validator($this);
    }
    
    return $this->validator;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getRenderer()
  {
    if ( !isset($this->renderer) ){
      $this->renderer = new Renderer($this);
    }
    
    return $this->renderer;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getName()
  {
    return $this->name;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getContext()
  {
    if ( strpos($this->name, '.') !== false ){
      list($type, $name) = explode('.', $this->name, 2);
    }
    else {
      $name = $this->name;
    }
    return $name;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getValue($name, $group=null, $default=null)
  {
    if ( $group ){
      return $this->data->get($group.'.'.$name, $default);
    }

    return $this->data->get($name, $default);
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFieldsets($group=null)
  {
    $fieldsets = [];
    $sets      = [];

    if ( $group ){
      $elements =& $this->findGroup($group);

      foreach($elements as &$element){
        // Get an array of <fieldset /> elements and fieldset attributes within the fields element.
        if ( $tmp = $element->xpath('descendant::fieldset[@name] | descendant::field[@fieldset]/@fieldset') ){
          $sets = array_merge($sets, (array) $tmp);
        }
      }
    }
    else {
      $sets = $this->xml->xpath('//fieldset[@name] | //field[@fieldset]/@fieldset');
    }

    if ( empty($sets) ){
      return $fieldsets;
    }
    
    foreach($sets as $set){
      if ( (string) $set['name'] ){
        if ( empty($fieldsets[(string) $set['name']]) ){
          $fieldset = (object) [ 'name' => '', 'label' => '', 'description' => '' ];
          foreach($set->attributes() as $name => $value){
            $fieldset->$name = (string) $value;
          }
          $fieldsets[$fieldset->name] = $fieldset;
        }
      }
      else {
        if ( empty($fieldsets[(string) $set]) ){
          $tmp = $this->xml->xpath('//fieldset[@name="' . (string) $set . '"]');
          
          if ( empty($tmp) ){
            $fieldset = (object)[ 'name' => (string) $set, 'label' => '', 'description' => '' ];
          }
          else {
            $fieldset = (object) [ 'name' => '', 'label' => '', 'description' => '' ];
            foreach($tmp[0]->attributes() as $name => $value){
              $fieldset->$name = (string) $value;
            }
          }
          
          $fieldsets[$fieldset->name] = $fieldset;
        }
      }
    }

    return $fieldsets;
  }

  /**
   * {@inheritDoc}
   */
  public function getGroup($group, $nested=false)
  {
    $fields = [];

    $elements = $this->findFieldsByGroup($group, $nested);

    if ( empty($elements) ){
      return $fields;
    }

    foreach($elements as $element){
      $attrs  = $element->xpath('ancestor::fields[@name]/@name');
      $groups  = array_map('strval', $attrs ? $attrs : []);
      $group  = implode('.', $groups);

      if ( $field = FormHelper::loadField($this, $element, $group) ){
        $fields[$field->id] = $field;
      }
    }

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function getField($name, $group=null, $value=null)
  {
    $element = $this->findField($name, $group);
    if ( !$element ){
      return false;
    }
    
    return FormHelper::loadField($this, $element, $group, $value);
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFieldAttribute($name, $attribute, $default=null, $group=null)
  {
    $element = $this->findField($name, $group);
    
    if ( ($element instanceof SimpleXMLElement) && ((string) $element[$attribute]) ){
      return (string) $element[$attribute];
    }

    return $default;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFieldset($set=null)
  {
    $fields = [];
    
    if ( $set ){
      $elements = $this->findFieldsByFieldset($set);
    }
    else {
      $elements = $this->findFieldsByGroup();
    }
    
    if ( empty($elements) ){
      return $fields;
    }
    
    foreach($elements as $element){
      $attrs  = $element->xpath('ancestor::fields[@name]/@name');
      $groups = array_map('strval', $attrs ? $attrs : []);
      $group  = implode('.', $groups);

      if ( $field = FormHelper::loadField($this, $element, $group) ){
        $fields[$field->get('id')] = $field;
      }
    }
    
    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function getError($i=null, $toString=true)
  {
    if ( $i === null ){
      $error = end($this->errors);
    }
    else {
      if ( !array_key_exists($i, $this->errors) ){
        return false;
      }
      
      $error = $this->errors[$i];
    }
    
    if ( $error instanceof Exception ){
      return $error->getMessage();
    }
    
    return $error;
  }
  
  /**
   * {@inheritDoc}
   */
  public function removeField($name, $group=null)
  {
    $element = $this->findField($name, $group);
    
    if ( $element instanceof SimpleXMLElement ){
      $dom = dom_import_simplexml($element);
      $dom->parentNode->removeChild($dom);
    }
    
    return true;
  }

  /**
   * {@inheritDoc}
   */
  public function removeGroup($group)
  {
    $elements =& $this->findGroup($group);
    foreach($elements as &$element){
      $dom = dom_import_simplexml($element);
      $dom->parentNode->removeChild($dom);
    }
    return true;
  }
  
  /**
   * {@inheritDoc}
   */
  public function &findFieldsByGroup($group=null, $nested=false)
  {
    $false = false;
    $fields = [];

    if ( $group ){
      $elements = &$this->findGroup($group);
      
      foreach($elements as $element){
        if ( $tmp = $element->xpath('descendant::field') ){
          if ( $nested ){
            $fields = array_merge($fields, $tmp);
          }
          else {
            $groupNames = explode('.', $group);
            foreach($tmp as $field){
              $attrs = $field->xpath('ancestor::fields[@name]/@name');
              $names = array_map('strval', $attrs ? $attrs : []);

              if ( $names == (array) $groupNames ){
                $fields = array_merge($fields, array($field));
              }
            }
          }
        }
      }
    }
    elseif ( $group === false ){
      $fields = $this->xml->xpath('descendant::fields[not(@name)]/field | descendant::fields[not(@name)]/fieldset/field ');
    }
    else {
      $fields = $this->xml->xpath('//field');
    }

    return $fields;
  }
  
  /**
   * Get an array of <field /> elements from the form XML document which are
   * in a specified fieldset by name.
   * 
   * @param   string    $name   The name of the fieldset.
   * @return   mixed     Array of SimpleXMLElement objects.
   */
  protected function &findFieldsByFieldset($name)
  {
    $fields = $this->xml->xpath('//fieldset[@name="' . $name . '"]//field | //field[@fieldset="' . $name . '"]');
    return $fields;
  }
  
  /**
   * Get a form field represented as an XML element object.
   * @param   string  $name   The name of the form field.
   * @param   string  $group  The optional dot-separated form group path on which to find the field.
   * @return   mixed  The XML element object for the field or boolean false on error.
   */
  protected function findField($name, $group=null)
  {
    $element = false;
    $fields = [];

    if ( $group ){
      $elements =& $this->findGroup($group);
      foreach($elements as $element){
        if ( $tmp = $element->xpath('descendant::field[@name="' . $name . '"]') ){
          $fields = array_merge($fields, $tmp);
        }
      }
      
      if ( !$fields ){
        return false;
      }
      
      $groupNames = explode('.', $group);
      foreach($fields as &$field){
        $attrs = $field->xpath('ancestor::fields[@name]/@name');
        $names = array_map('strval', $attrs ? $attrs : []);

        if ( $names == (array) $groupNames ){
          $element =& $field;
          break;
        }
      }
    }
    else {
      $fields = $this->xml->xpath('//field[@name="' . $name . '"]');

      if ( !$fields ){
        return false;
      }
      
      foreach($fields as &$field){
        if ( $field->xpath('ancestor::fields[@name]') ){
          continue;
        }
        
        $element =& $field;
        break;
      }
    }
    
    return $element;
  }
  
  /**
   * Apply an input filter to a value based on field data.
   * 
   * @param   SimpleXMLElement   $element  The XML element object representation of the form field.
   * @param   mixed               $value    The value to filter for the field.
   * @return   mixed   The filtered value.
   * @todo     Extract this to stringHelper
   */
  protected function filterField(FieldInterface $field, $value)
  {
    $filter = (string)$field->get('filter');
    
    $return = null;

    switch(strtoupper($filter)){
      // Do nothing, thus leaving the return value as null.
      case 'UNSET':
        break;
      
      // No Filter.
      case 'RAW':
        $return = $value;
        break;
      
      case 'INT_ARRAY':
        if ( is_object($value) ){
          $value = get_object_vars($value);
        }
        $value = is_array($value) ? $value : [$value];
        
        ArrayHelper::toInteger($value);
        $return = $value;
        break;

      case 'SAFEHTML':
        $value = (string) $value;
        break;
      
      case 'SERVER_UTC':
        if ( intval($value) > 0 ){
          $return = DateObject::getInstance($value)->toSql();
        }
        else {
          $return = '';
        }
        break;
      
      case 'URL':
        if ( empty($value) ){
          return false;
        }
        
        $value    = (string) strip_tags($value);
        $value    = trim($value);
        $value    = str_replace(array('<', '>', '"'), '', $value);
        $protocol = parse_url($value, PHP_URL_SCHEME);
        
        if ( !$protocol ){
          $value = 'http://'.$value;
        }
        
        $return = $value;
        break;
      
      case 'TEL':
        $value = trim($value);
        
        if ( $pattern = $field->get('pattern') ){
          if ( preg_match('/'.$pattern.'/', $value) !== false ){
            $result = $value;
          }
          else {
            $result = '';
          }
        }
        else {
          $value = (string) preg_replace('/[^\d]/', '', $value);
          if ( $value != null && strlen($value) <= 15 ){
            $length = strlen($value);
            if ( $length <= 12 ){
              $result = '.' . $value;
            }
            else {
              $cclen = $length - 12;
              $result = substr($value, 0, $cclen) . '.' . substr($value, $cclen);
            }
          }
          else {
            $result = '';
          }
        }
        
        $return = $result;
        break;
      
      default:
        if ( strpos($filter, '::') !== false && is_callable(explode('::', $filter)) ){
          $return = call_user_func(explode('::', $filter), $value);
        }
        elseif ( function_exists($filter) ){
          $return = call_user_func($filter, $value);
        }
        else {
          $return = $value;
        }
        break;
    }

    return $return;
  }
  
  /**
   * Bind data to the form for the group level.
   * @param   string  $group  The dot-separated form group path on which to bind the data.
   * @param   mixed   $data   An array or object of data to bind to the form for the group level.
   * @return   void
   */
  protected function bindLevel($group, $data)
  {
    settype($data, 'array');

    foreach($data as $k => $v){
      if ( $this->findField($k, $group) ){
        $this->data->set($group . '.' . $k, $v);
      }
      elseif ( is_object($v) || ArrayHelper::isAssociative($v) ){
        $this->bindLevel($group . '.' . $k, $v);
      }
    }
  }
  
  /**
   * Get a form field group represented as an XML element object.
   * 
   * @param   string    $group      The dot-separated form group path on which to find the group.
   * @return   mixed     An array of XML element objects for the group or boolean false on error.
   * @throws   Exception
   */
  protected function &findGroup($group)
  {
    $false = false;
    $groups = [];
    $tmp = [];

    $group = explode('.', $group);
    if ( !empty($group) ){
      $elements = $this->xml->xpath('//fields[@name="' . (string) $group[0] . '"]');

      foreach($elements as $element){
        if ( !$element->xpath('ancestor::fields[@name]') ){
          $tmp[] = $element;
        }
      }

      for($i=1, $n=count($group); $i<$n; $i++){
        $validNames = array_slice($group, 0, $i + 1);
        $current = $tmp;
        $tmp = [];

        foreach($current as $element){
          $children = $element->xpath('descendant::fields[@name="' . (string) $group[$i] . '"]');

          foreach ($children as $fields){
            $attrs = $fields->xpath('ancestor-or-self::fields[@name]/@name');
            $names = array_map('strval', $attrs ? $attrs : []);

            if ( $validNames == $names ){
              $tmp[] = $fields;
            }
          }
        }
      }
      
      foreach($tmp as $element){
        if ( $element instanceof SimpleXMLElement ){
          $groups[] = $element;
        }
      }
    }
    
    return $groups;
  }

  /**
   * Synchronize any field, form or rule paths contained in the XML document.
   * 
   * @return   boolean  True on success.
   */
  protected function syncPaths()
  {
    return ( ($this->xml instanceof SimpleXMLElement) );
  }
}
