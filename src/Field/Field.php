<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Field;

use JDZ\Form\Form;
use JDZ\Form\FormHelper;
use JDZ\Registry\Registry;
use SimpleXMLElement;
use RuntimeException;

/**
 * Abstract Field
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class Field implements FieldInterface
{
  use \JDZ\Utilities\Traits\Get,
      \JDZ\Utilities\Traits\Set;
  
  /**
   * Form 
   * 
   * @var    Form   
   */
  protected $form;
  
  /**
   * Field XML definition
   * 
   * @var    SimpleXMLElement   
   */
  protected $element;
  
  /**
   * Field value
   * 
   * @var    string   
   */
  protected $value;
  
  /**
   * Field group
   * 
   * @var    string   
   */
  protected $group;
  
  /**
   * Field is static
   * 
   * @var    bool
   */
  protected $static;
  
  /**
   * Field is hidden
   * 
   * @var    bool   
   */
  protected $hidden;
  
  /**
   * Field id attribute
   * 
   * @var    string   
   */
  protected $id;
  
  /**
   * Field name attribute
   * 
   * @var    string   
   */
  protected $name;
  
  /**
   * Field required attribute
   * 
   * @var    bool   
   */
  protected $required;
  
  /**
   * Field readonly attribute
   * 
   * @var    bool   
   */
  protected $readonly;
  
  /**
   * Field disabled attribute
   * 
   * @var    bool   
   */
  protected $disabled;
  
  /**
   * Field multiple attribute
   * 
   * @var    bool   
   */
  protected $multiple;
  
  /**
   * Field autofocus attribute
   * 
   * @var    bool   
   */
  protected $autofocus;
  
  /**
   * Field width attribute
   * 
   * @var    int   
   */
  protected $width;
  
  /**
   * Field class value
   * 
   * @var    string   
   */
  protected $class;
  
  /**
   * Field default value
   * 
   * @var    string   
   */
  protected $default;
  
  /**
   * Field description value
   * 
   * @var    string   
   */
  protected $description;
  
  /**
   * Field validation filter
   * 
   * @var    string   
   */
  protected $filter;
  
  /**
   * Hide field when empty value
   * 
   * @var    bool   
   */
  protected $hideWhenEmpty;
  
  /**
   * Field can be rendered as static 
   * 
   * @var    bool   
   */
  protected $canBeStatic;
  
  /**
   * The field container class
   * 
   * @var    string
   */
  protected $containerClass;
  
  /**
   * The field label class
   * 
   * @var    string
   */
  protected $labelClass;
  
  /**
   * The field label text
   * 
   * @var    string
   */
  protected $labelText;
  
  /**
   * Hide field label
   * 
   * @var    bool   
   */
  protected $labelHide;
  
  /**
   * Validate methods
   * 
   * @var    string   
   */
  protected $validate;
  
  /**
   * Required field message
   * 
   * @var    string   
   */
  protected $message;
  
  /**
   * Boostrap3 inputgroup prefix
   * 
   * @var    string   
   */
  protected $bsInputgroupPrefix;
  
  /**
   * Boostrap3 inputgroup suffix
   * 
   * @var    string   
   */
  protected $bsInputgroupSuffix;
  
  /**
   * Boostrap3 inputgroup class
   * 
   * @var    string   
   */
  protected $bsInputgroupClass;
  
  /**
   * Fields namepace
   * 
   * @var    string   
   */
  protected static $NS;
  
  /**
   * Field instances
   * 
   * @var    array   
   */
  protected static $instances;
  
  /**
   * Get a field instance
   * 
   * @param   string            $type   The field type
   * @return   FieldInterface    Field instance clone
   * @throws   RuntimeException 
   */
  public static function getInstance($type)
  {
    if ( !isset(self::$NS) ){
      self::$NS = '\\Form\\Field\\';
    }
    
    if ( !isset(self::$instances) ){
      self::$instances = [];
    }
    
    if ( empty($type) ){
      throw new RuntimeException('Missing field type');
    }
    
    if ( $type === 'datetime-local' ){
      $type = 'datetime';
    }
    
    $type = str_replace('-', '', $type);
    
    if ( !isset(self::$instances[$type]) ){
      $Class = self::$NS.ucfirst($type);
      
      if ( !class_exists($Class) ){
        throw new RuntimeException('Unrecognized field type :: '.$type);
      }
      
      self::$instances[$type] = new $Class();
    }
    
    return clone self::$instances[$type];
  }
  
  /**
   * Set the field namespace
   * 
   * @param   string  $NS  The field namespace
   * @return  void
   */
  public static function setNamespace($NS)
  {
    self::$NS = $NS;
  }
  
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->multiple = false;
    $this->static   = false;
  }
  
  /**
   * {@inheritDoc}
   */
  public function init(Form &$form, SimpleXMLElement &$element, $group=null, $value=null)
  {
    $this->setForm($form);
    $this->setElement($element);
    $this->setGroup($group);
    $this->initDefinition();
    $this->initObject();
    $this->buildId();
    $this->buildName();
    $this->onReady();
    $this->checkValidate();
    $this->setValue($value);
  }
  
  /**
   * {@inheritDoc}
   */
  public function setAttribute($attribute, $value='', $type='string')
  {
    $this->element[$attribute] = $value;
  }
  
  /**
   * {@inheritDoc}
   */
  public function defAttribute($attribute, $default=null, $type='string')
  {
    $attrs = $this->element->attributes();
    if ( !isset($attrs[$attribute]) ){
      $this->element[$attribute] = null;
    }
    
    $value = (string)$this->element[$attribute];
    
    if ( $type === 'bool' ){
      if ( $value === '' || $value === '0' || $value === 'false' ){
        $value = 'false';
      }
      else {
        $value = 'true';
      }
    }
    elseif ( $type === 'int' ){
      if ( $value === '' ){
        $value = $default === null ? '0' : (string)$default;
      }
    }
    else {
      if ( $value === '' ){
        $value = $default === null ? '' : (string)$default;
      }
    }
    /* if ( $type === 'bool' ){
      if ( empty($value) ){
        $value = $default === null ? 'false' : $default;
      }
    }
    elseif ( $type === 'int' ){
      if ( empty($value) ){
        $value = $default === null ? '0' : $default;
      }
    }
    else {
      if ( empty($value) ){
        $value = $default === null ? '' : $default;
      }
    } */
    
    $this->setAttribute($attribute, $value);
  }
  
  /**
   * {@inheritDoc}
   */
  public function cleanForRender()
  {
    $this->checkValue();
    $this->checkValidate();
    $this->checkState();
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFieldHtml(array $attrs=[])
  {
    return $this->renderField($attrs);
  }
  
  /**
   * {@inheritDoc}
   */
  public function getStaticValue()
  {
    return FormHelper::formatStaticValue($this->value);
  }
  
  /**
   * {@inheritDoc}
   */
  public function getHiddenValue()
  {
    return FormHelper::formatHiddenValue($this->value);
  }
  
  /**
   * {@inheritDoc}
   */
  public function isEmpty()
  {
    return ( $this->value === '' );
  }
  
  /**
   * {@inheritDoc}
   */
  public function isHidden()
  {
    return ( $this->hidden );
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFieldAttributes(array $attrs=[])
  {
    $attrs['id']    = $this->id;
    $attrs['name']  = $this->name;
    $attrs['class'] = $this->getFieldClass();
    
    if ( $this->readonly === true ){
      $attrs['readonly'] = 'readonly';
    }
    
    if ( $this->disabled === true ){
      $attrs['disabled'] = 'disabled';
    }
    
    // if ( $this->required === true ){
      // $attrs['required'] = 'required';
    // }
    
    if ( $this->autofocus === true ){
      $attrs['autofocus'] = 'autofocus';
    }
    
    if ( $this->width > 0 ){
      $attrs['width'] = $this->width;
    }
    
    return $attrs;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getContainerClass()
  {
    $classes = [];
    
    if ( $_classes = $this->form->getOption('fieldCols') ){
      $_classes = explode(' ', $_classes);
      $classes = array_merge($classes, $_classes);
    }
    
    if ( $this->containerClass !== '' ){
      $_classes = explode(' ', $this->containerClass);
      $classes = array_merge($classes, $_classes);
    }
    
    if ( $this->labelHide === true ){
      foreach($classes as $i => $v){
        if ( substr($v, 0, 4) === 'col-' ){
          unset($classes[$i]);
          continue;
        }
      }
      $offset = preg_replace("/[^\d]/", "", Form::BS_COL_LABEL);
      $width  = preg_replace("/[^\d]/", "", Form::BS_COL_FIELD);
      $classes[] = 'col-xs-12 col-sm-offset-'.$offset.' col-sm-'.$width;
    }
    
    return $classes;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getLabelClass()
  {
    $classes = [];
    
    if ( $this->labelHide === false ){
      if ( $_classes = $this->form->getOption('labelCols') ){
        $_classes = explode(' ', $_classes);
        $classes = array_merge($classes, $_classes);
      }
      
      if ( $this->form->getOption('type') === 'inline' ){
        $classes[] = 'sr-only';
      }
      elseif ( $this->form->getOption('type') === 'horizontal' ){
        $classes[] = 'control-label';
      }
      
      if ( $this->labelClass !== '' ){
        $_classes = explode(' ', $this->labelClass);
        $classes = array_merge($classes, $_classes);
      }
      
      if ( $this->required === true ){
        $classes[] = 'required';
      }      
    }
    
    return $classes;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFieldClass()
  {
    $classes = [];
    $classes[] = 'form-control';
    
    if ( $this->class !== '' ){
      $_classes = explode(' ', $this->class);
      $classes  = array_merge($classes, $_classes);
    }
    
    if ( $this->required === true ){
      $classes[] = 'required';
    }
    
    if ( $this->readonly === true ){
      $classes[] = 'readonly';
    }
    
    if ( $this->disabled === true ){
      $classes[] = 'disabled';
    }
    
    return $classes;
  }
  
  /**
   * Set the form singleton
   * 
   * @param   Form    $form   Reference to the parent form
   * @return   void
   */
  protected function setForm(Form &$form)
  {
    $this->form =& $form;
  }
  
  /**
   * Set the value
   * 
   * @param   mixed    $value   The field value
   * @return   void
   */
  protected function setElement(SimpleXMLElement &$element)
  {
    if ( empty($element['name']) ){
      throw new RuntimeException('Missing field name in XML definition');
    }    
    
    $this->element =& $element;
  }
  
  /**
   * Set the group
   * 
   * @param   mixed    $value   The field group
   * @return   void
   */
  protected function setGroup($group=null)
  {
    $this->group = $group;
  }
  
  /**
   * Prepare the xml definition data
   *
   * @return   void
   */
  protected function initDefinition()
  {
    // $attrs = $this->element->attributes();
    
    $this->defAttribute('id', ''); // $this->element['name']);
    $this->defAttribute('required', 'false', 'bool');
    $this->defAttribute('readonly', 'false', 'bool');
    $this->defAttribute('disabled', 'false', 'bool');
    $this->defAttribute('autofocus', 'false', 'bool');
    $this->defAttribute('hidden', 'false', 'bool');
    $this->defAttribute('hideWhenEmpty', 'false', 'bool');
    $this->defAttribute('labelHide', 'false', 'bool');
    $this->defAttribute('canBeStatic', 'false', 'bool');
    
    $this->defAttribute('width', '0', 'int');
    
    $this->defAttribute('default', '');
    $this->defAttribute('description', '');
    $this->defAttribute('filter', '');
    $this->defAttribute('class', '');
    $this->defAttribute('containerClass', '');
    $this->defAttribute('labelClass', '');
    $this->defAttribute('labelText', '');
    $this->defAttribute('validate', '');
    $this->defAttribute('message', '');
    
    $this->defAttribute('bsInputgroupPrefix', '');
    $this->defAttribute('bsInputgroupSuffix', '');
    $this->defAttribute('bsInputgroupClass', '');
  }
  
  /**
   * Setup the field data
   *
   * @return   void
   */
  protected function initObject()
  {
    $this->required       = ( (string) $this->element['required'] === 'true' );
    $this->readonly       = ( (string) $this->element['readonly'] === 'true' );
    $this->disabled       = ( (string) $this->element['disabled'] === 'true' );
    $this->multiple       = ( (string) $this->element['multiple'] === 'true' );
    $this->autofocus      = ( (string) $this->element['autofocus'] === 'true' );
    $this->hidden         = ( (string) $this->element['hidden'] === 'true' );
    $this->hideWhenEmpty  = ( (string) $this->element['hideWhenEmpty'] === 'true' );
    $this->canBeStatic    = ( (string) $this->element['canBeStatic'] === 'true' );
    $this->labelHide      = ( (string) $this->element['labelHide'] === 'true' );
    
    $this->width          = (int) $this->element['width'];
    
    $this->default        = (string) $this->element['default'];
    $this->description    = (string) $this->element['description'];
    $this->filter         = (string) $this->element['filter'];
    $this->validate       = (string) $this->element['validate'];
    $this->class          = (string) $this->element['class'];
    $this->containerClass = (string) $this->element['containerClass'];
    $this->labelClass     = (string) $this->element['labelClass'];
    $this->labelText      = (string) $this->element['labelText'];
    
    $this->bsInputgroupPrefix = (string) $this->element['bsInputgroupPrefix'];
    $this->bsInputgroupSuffix = (string) $this->element['bsInputgroupSuffix'];
    $this->bsInputgroupClass  = (string) $this->element['bsInputgroupClass'];
  }
  
  /**
   * Get the id used for the field input tag.
   * 
   * @param   string  $fieldId    The field element id.
   * @param   string  $fieldName  The field element name.
   * @return   string  The id to be used for the field input tag.
   */
  protected function buildId()
  {
    $fieldid = (string) $this->element['id'];
    
    if ( empty($fieldid) ){
      $fieldid = (string) $this->element['name'];
    }
    
    $this->id = '';
    
    if ( $control = $this->form->getOption('control') ){
      $this->id .= $control;
    }
    
    // If the field is in a group add the group control to the field id.
    if ( $this->group ){
      // If we already have an id segment add the group control as another level.
      if ( $this->id !== '' ){
        $this->id .= '_'.str_replace('.', '_', $this->group);
      }
      else {
        $this->id .= str_replace('.', '_', $this->group);
      }
    }

    // If we already have an id segment add the field id as another level.
    if ( $this->id !== '' ){
      $this->id .= '_'.$fieldid;
    }
    else {
      $this->id .= $fieldid;
    }
    
    // Clean up any invalid characters.
    $this->id = preg_replace('#\W#', '_', $this->id);
  }
  
  /**
   * Get the name used for the field input tag.
   * 
   * @param   string  $fieldName  The field element name.
   * @return   string  The name to be used for the field input tag.
   */
  protected function buildName()
  {
    $fieldname = (string) $this->element['name'];
    
    $this->name = '';

    if ( $control = $this->form->getOption('control') ){
      $this->name .= $control;
    }
    
    if ( $this->group ){
      // If we already have a name segment add the group control as another level.
      $groups = explode('.', $this->group);
      if ( $this->name !== '' ){
        foreach($groups as $group){
          $this->name .= '['.$group.']';
        }
      }
      else {
        $this->name .= array_shift($groups);
        foreach($groups as $group){
          $this->name .= '['.$group.']';
        }
      }
    }
    
    if ( $this->name !== '' ){
      $this->name .= '['.$fieldname.']';
    }
    else {
      $this->name .= $fieldname;
    }
    
    if ( $this->multiple === true ){
      $this->name .= '[]';
    }
  }
  
  /**
   * When properties are ready to use
   *
   * @return   void
   */
  protected function onReady()
  {
  }
  
  /**
   * Set the value
   * 
   * @param   mixed    $value   The field value
   * @return   void
   */
  protected function setValue($value=null)
  {
    $this->value = $value === null ? '' : $value;
    
    $this->checkValue();
  }
  
  /**
   * Check the field value
   *
   * @return   void
   */
  protected function checkValue()
  {
    if ( $this->filter === 'int' ){
      if ( $this->value !== '' ){
        if ( intval($this->value) === 0 ){
          $this->value = '';
        }
      }
      
      return;
    }
    
    if ( $this->filter === 'bool' ){
      if ( empty($value) ){ //|| (string)$value === '0' || (string)$value === 'false' || (string) $value === '' ){
        $this->value = '';
      }
      else {
        $this->value = '1';
      }
      
      return;
    }
    
    if ( $this->value === '' ){
      $this->value = $this->default;
    }
  }
  
  /**
   * Check the field validate parameters 
   *
   * @return   void
   */
  protected function checkValidate()
  {
    if ( is_array($this->validate) ){
      $this->validate = implode('|', $this->validate);
    }
    
    $this->validate = (string)$this->validate;
    
    if ( $this->validate === '' ){
      $this->validate = [];
    }
    else {
      $this->validate = explode('|', $this->validate);      
    }
  }
  
  /**
   * Check the field static & readonly state
   *
   * @return   void
   */
  protected function checkState()
  {
    if ( $this->canBeStatic === true ){
      if ( $this->readonly === true ){
        if ( $this->hideWhenEmpty === true && $this->isEmpty() ){
          $this->hidden = true;
        }
        else {
          $this->static = true;
        }
      }
    }
  }
  
  /**
   * Render the field
   * 
   * @param   string  $indent  HTML indent
   * @return   string  HTML
   */
  abstract protected function renderField(array $attrs=[]);
}
