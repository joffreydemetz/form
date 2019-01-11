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
use JDZ\Form\Field\Field;
use JDZ\Form\Exception\RequiredException;
use JDZ\Form\Exception\InvalidException;
use JDZ\Helpers\ArrayHelper;
use JDZ\Form\FormData;
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
   * Form namespace
   * 
   * Used to include Forms, Fields, Rules, ..
   * 
   * @var   string
   */
  public static $ns = '\\Form';
  
  /**
   * The name of the form instance
   * 
   * @var    string 
   */
  protected $name;

  /**
   * Input control
   * 
   * @var    string
   */
  protected $inputControlName = 'csForm';
  
  /**
   * Update mode
   * 
   * @var    string
   */
  protected $updateMode = false;
  
  /**
   * Component
   * 
   * @var    string
   */
  protected $component;
  
  /**
   * i18n namespace
   * 
   * @var    string
   */
  protected $i18nNamespace;
  
  /**
   * Form orientation
   * 
   * @var    string   'vertical' or  'inline' or 'horizontal'
   */
  protected $orientation = 'vertical';
  
  /**
   * Form XML definition
   * 
   * @var    SimpleXMLElement 
   */
  protected $xml;
  
  /**
   * Form field values
   * 
   * @var    FormData   
   */
  protected $data;

  /**
   * Renderer instance
   * 
   * @var    Renderer 
   */
  protected $renderer;
  
  /**
   * Form Validator instance
   * 
   * @var    Validator 
   */
  protected $validator;
  
  /**
   * Fields 
   * 
   * @var    [FieldInterface]
   */
  protected $fields = [];

  /** 
   * An array of error messages or Exception objects
   * 
   * @var   array 
   */ 
  protected $errors = [];
  
  /**
   * Instantiate the form object
   * 
   * @param  string  $name  The name of the form
   */
  public function __construct($name)
  {
    $this->name   = $name;
    $this->data   = new FormData();
  }
  
  public function setXml(SimpleXMLElement $xml)
  {
    if ( 'form' !== $xml->getName() ){
      throw new RuntimeException('Invalid XML form ['.$xml->getName().']');
    }
    
    $formAttributes = $xml->attributes();
    
    $this->xml = $xml;
    
    $this->syncPaths();
    return $this;
  }
  
  public function setData(FormData $data, $reset=false)
  {
    if ( $reset ){
      $this->data = new FormData();
    }
    $this->bindData($data);
    return $this;
  }
  
  public function setUpdateMode($updateMode)
  {
    $this->updateMode = $updateMode;
    return $this;
  }
  
  public function setComponent($component)
  {
    $this->component = $component;
    return $this;
  }
  
  public function setI18nNamespace($i18nNamespace)
  {
    $this->i18nNamespace = $i18nNamespace;
    return $this;
  }
  
  public function setOrientation($orientation)
  {
    $this->orientation = $orientation;
    return $this;
  }
  
  public function setInputControlName($inputControlName)
  {
    $this->inputControlName = $inputControlName;
    return $this;
  }
  
  public function setRenderer($name)
  {
    $Class = '\\JDZ\\Form\\Renderer\\'.ucfirst($name).'Renderer';
    if ( !class_exists($Class) ){
      throw new RuntimeException('Form renderer '.$name.' not found');
    }
    
    $this->renderer = new $Class();
    $this->renderer->setForm($this);
    $this->renderer->addHiddenField(CrsfToken(), '1');
    
    return $this;
  }
  
  public function setValidator()
  {
    $this->validator = new Validator();
    $this->validator->setForm($this);
    return $this;
  }
  
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
    return $this;
  }
  
  public function setFieldAttribute($name, $attribute, $value, $group=null)
  {
    $element = $this->findField($name, $group);
    
    if ( !($element instanceof SimpleXMLElement) ){
      throw new RuntimeException(__CLASS__ .'->'. __METHOD__ . ' Invalid SimpleXMLElement for '.$name); 
    }
    
    $element[$attribute] = $value;
    
    $this->syncPaths();
    
    return $this;
  }
  
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
    
    return $this;
  }
  
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
  
  public function getNs()
  {
    return static::$ns;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function getOrientation()
  {
    return $this->orientation;
  }
  
  public function getInputControlName()
  {
    return $this->inputControlName;
  }
  
  public function getRenderer()
  {
    return $this->renderer;
  }
  
  public function getValidator()
  {
    return $this->validator;
  }
  
  public function getXml()
  {
    return $this->xml;
  }
  
  public function getData()
  {
    return $this->data;
  }
  
  public function getErrorsAsString($separator='<br />')
  {
    $errors = [];
    
    foreach($this->errors as &$error){
      $errors[] = $error->message;
    }
    
    return implode($separator, $errors);
  }
  
  public function getErrors()
  {
    return $this->errors;
  }
  
  public function getComponent()
  {
    return $this->component;
  }
  
  public function getI18nNamespace()
  {
    if ( !isset($this->i18nNamespace) ){
      $this->i18nNamespace = $this->component;
    }
    return $this->i18nNamespace;
  }
  
  public function getValue($name, $group=null, $default=null)
  {
    if ( $group ){
      return $this->data->get($group.'.'.$name, $default);
    }
    return $this->data->get($name, $default);
  }
  
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
    
    // if ( !empty($sets) ){
      foreach($sets as $set){
        $fieldsetName        = (string)$set['name'];
        $fieldsetLabel       = (string)$set['label'];
        $fieldsetDescription = (string)$set['description'];
        $fieldset = (object) [ 
          'name' => $fieldsetName, 
          'label' => $fieldsetLabel, 
          'description' => $fieldsetDescription,
        ];
        $fieldsets[$fieldsetName] = $fieldset;
      }
    // }

    return $fieldsets;
  }

  /* public function getFieldsets($group=null)
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
  } */

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

      if ( $field = $this->getField($element, $group) ){
        $fields[$field->id] = $field;
      }
    }

    return $fields;
  }

  public function getField(SimpleXMLElement $element, $group=null, $value=null)
  {
    if ( !$element ){
      return false;
    }
    
    $name = (string) $element['name'];
    if ( !$name ){
      return false;
    }
    
    if ( null === $value ){
      $value = $this->getValue($name, $group);
    }
    
    if ( !isset($this->fields[$name]) ){
      $type = (string) $element['type'];
      
      $this->fields[$name] = Field::create($type)
        ->setForm($this)
        ->setElement($element)
        ->setGroup($group)
        ->init();
    }
    
    $this->fields[$name]->setValue($value); 
    
    return $this->fields[$name];
  }
  
  public function getFieldAttribute($name, $attribute, $default=null, $group=null)
  {
    $element = $this->findField($name, $group);
    
    if ( ($element instanceof SimpleXMLElement) && ((string) $element[$attribute]) ){
      return (string) $element[$attribute];
    }

    return $default;
  }
  
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

      if ( $field = $this->getField($element, $group) ){
        $fields[$field->getId()] = $field;
      }
    }
    
    return $fields;
  }

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
  
  public function removeField($name, $group=null)
  {
    $element = $this->findField($name, $group);
    
    if ( $element instanceof SimpleXMLElement ){
      $dom = dom_import_simplexml($element);
      $dom->parentNode->removeChild($dom);
    }
    
    return true;
  }

  public function isVertical()
  {
    return 'horizontal' !== $this->orientation && 'inline' !== $this->orientation;
  }
  
  public function isHorizontal()
  {
    return 'horizontal' === $this->orientation;
  }
  
  public function isInline()
  {
    return 'inline' === $this->orientation;
  }
  
  public function isUpdateMode()
  {
    return true === $this->updateMode;
  }
  
  public function filter(array $data, $group=null)
  {
    $fields = $this->findFieldsByGroup($group);
    if ( !$fields ){
      throw new RuntimeException('No fields found for '.$group);
    }
    
    $input  = new FormData($data);
    $output = new FormData();
    
    foreach($fields as $element){
      $attrs   = $element->xpath('ancestor::fields[@name]/@name');
      $groups  = array_map('strval', $attrs ? $attrs : []);
      $group   = implode('.', $groups);
      $name    = (string) $element['name'];
      
      $key = ($group===''?'':$group.'.').$name;
      
      $field = $this->getField($element, $group, $input->get($key, ''));
      $element = $field->get('element');
      $filter  = (string) $element['filter'];
      $default = (string) $element['default'];
      
      if ( empty($filter) ){
        $filter = 'string';
      }
      /* elseif ( is_callable($filter) ){
        $filter = 'raw';
      } */
      
      if ( $input->has($key) ){
        $output->set($key, $this->filterField($field, $input->get($key, $default, $filter)));
      }
    }
    
    return $output;
  }
  
  public function validate(FormData $data)
  {
    return $this->validator->execute($data);
  }
  
  /**
   * Bind data to the form
   * 
   * @param   FormData|array|object  $data  An array or object of data to bind to the form
   * @return  $this
   */
  protected function bindData($data, $group=null)
  {
    if ( $data instanceof FormData ){
      $data = $data->export();
    }
    
    foreach((array)$data as $k => $v){
      if ( $group ){
        $key = $group.'.'.$k;
      }
      else {
        $key = $k;
      }
      
      if ( $this->findField($k, $group) ){
        $this->data->set($key, $v);
      }
      elseif ( is_object($v) || ArrayHelper::isAssociative($v) ){
        $this->bindData($key, $v);
      }
    }
    
    return $this;
  }
  
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
