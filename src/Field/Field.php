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
   * Field short name
   * 
   * @var    string   
   */
  protected $fieldname;
  
  /**
   * Field group
   * 
   * @var    string   
   */
  protected $group;
  
  /**
   * Field value
   * 
   * @var    string   
   */
  protected $value;
  
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
   * Field goes with
   * 
   * @var    string  Another field name
   */
  protected $goeswith;
  
  /**
   * Field gone with
   * 
   * The field won't be displayed as is
   * 
   * @var    string  Another field name
   */
  protected $gonewith;
  
  /**
   * Full width field (no label)
   * 
   * @var    bool
   */
  protected $fullWidth;
  
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
   * Label sr only
   * 
   * @var    bool
   */
  protected $labelSrOnly;
  
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
   * Field autocomplete attribute
   * 
   * @var    string   
   */
  protected $autocomplete;
  
  public static function create($type)
  {
    if ( !$type ){
      $element['type'] = 'text';
    }
    
    if ( $type === 'datetime-local' ){
      $type = 'datetime';
    }
    
    $type = str_replace('-', '', $type);
    
    $Class = Form::$ns.'\\Field\\'.ucfirst($type);
    
    if ( !class_exists($Class) ){
      throw new RuntimeException('Unrecognized field type :: '.$type);
    }
    
    return new $Class();
  }
  
  public function __construct()
  {
    $this->multiple = false;
    $this->static   = false;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setForm(Form $form)
  {
    $this->form = $form;
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setElement(SimpleXMLElement $element)
  {
    if ( empty($element['name']) ){
      throw new RuntimeException('Missing field name in XML definition');
    }
    
    $this->element = $element;
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setGroup($group=null)
  {
    $this->group = $group;
    return $this;
  }
  
  /**
   * Set the value
   * 
   * @param   mixed    $value   The field value
   * @return   void
   */
  public function setValue($value=null)
  {
    $this->value = $value === null ? '' : $value;
    
    $this->checkValue();
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setAttribute($attribute, $value='', $type='string')
  {
    $this->element[$attribute] = $value;
    return $this;
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
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getForm()
  {
    return $this->form;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getElement()
  {
    return $this->element;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getGroup()
  {
    return $this->group;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getValue()
  {
    return $this->value;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getId()
  {
    return $this->id;
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
  public function getFieldAttributes(array $attrs=[])
  {
    $classes = $this->getFieldClasses();
    
    $attrs['id']    = $this->id;
    $attrs['name']  = $this->name;
    
    if ( true === $this->readonly ){
      $attrs['readonly'] = 'readonly';
    }
    
    if ( true === $this->disabled ){
      $attrs['disabled'] = 'disabled';
    }
    
    if ( true === $this->autofocus ){
      $attrs['autofocus'] = 'autofocus';
    }
    
    if ( $this->autocomplete ){
      $attrs['autocomplete'] = $this->autocomplete;
    }
    
    if ( $this->width > 0 ){
      $attrs['width'] = $this->width;
    }
    
    if ( $this->bsInputgroupPrefix ){
      $attrs['aria-describedby'] = 'bsigroup-'.$this->id;
    }
    
    $attrs['class'] = implode(' ', $classes);
    return $attrs;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getContainerClasses()
  {
    $classes = [];
    
    if ( $this->containerClass !== '' ){
      $_classes = explode(' ', $this->containerClass);
      $classes = array_merge($classes, $_classes);
    }
    
    return $classes;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getLabelClasses()
  {
    $classes = [];
    
    if ( !$this->labelHide ){
      if ( $this->labelClass !== '' ){
        $_classes = explode(' ', $this->labelClass);
        $classes = array_merge($classes, $_classes);
      }
      
      if ( true === $this->required ){
        $classes[] = 'required';
      }      
    }
    
    return $classes;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFieldClasses()
  {
    $classes = [];
    $classes[] = 'form-control';
    
    if ( $this->class !== '' ){
      $_classes = explode(' ', $this->class);
      $classes  = array_merge($classes, $_classes);
    }
    
    if ( true === $this->required ){
      $classes[] = 'required';
    }
    
    if ( true === $this->readonly ){
      $classes[] = 'readonly';
    }
    
    if ( true === $this->disabled ){
      $classes[] = 'disabled';
    }
    
    return $classes;
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
  public function init($value=null)
  {
    $this->initDefinition();
    $this->initObject();
    $this->buildId();
    $this->buildName();
    $this->onReady();
    $this->checkValidate();
    $this->setValue($value);
    return $this;
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
    $this->defAttribute('labelSrOnly', 'false', 'bool');
    $this->defAttribute('canBeStatic', 'false', 'bool');
    $this->defAttribute('fullWidth', 'false', 'bool');
    
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
    $this->defAttribute('autocomplete', '');
    $this->defAttribute('goeswith', '');
    $this->defAttribute('gonewith', '');
    
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
    $this->fullWidth      = ( (string) $this->element['fullWidth'] === 'true' );
    
    if ( $this->fullWidth ){
      $this->labelHide = true;
    }
    
    $this->width          = (int) $this->element['width'];
    
    $this->default        = (string) $this->element['default'];
    $this->description    = (string) $this->element['description'];
    $this->filter         = (string) $this->element['filter'];
    $this->validate       = (string) $this->element['validate'];
    $this->class          = (string) $this->element['class'];
    $this->containerClass = (string) $this->element['containerClass'];
    $this->labelClass     = (string) $this->element['labelClass'];
    $this->labelText      = (string) $this->element['labelText'];
    $this->autocomplete   = (string) $this->element['autocomplete'];
    $this->goeswith       = (string) $this->element['goeswith'];
    $this->gonewith       = (string) $this->element['gonewith'];
    
    $this->bsInputgroupPrefix = (string) $this->element['bsInputgroupPrefix'];
    $this->bsInputgroupSuffix = (string) $this->element['bsInputgroupSuffix'];
    $this->bsInputgroupClass  = (string) $this->element['bsInputgroupClass'];
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
   * Get the id used for the field input tag.
   * 
   * @param   string  $fieldId    The field element id.
   * @param   string  $fieldName  The field element name.
   * @return   string  The id to be used for the field input tag.
   */
  protected function buildId()
  {
    $fieldid = (string) $this->element['id'];
    
    if ( $fieldid ){
      $this->id = $fieldid;
    }
    else {
      $parts = [];
      
      if ( $control = $this->form->getInputControlName() ){
        $parts[] = $control;
      }
      
      $parts[] = $this->form->getComponent();
      
      if ( $this->group ){
        $parts[] = str_replace('.', '_', $this->group);
      }
      
      $parts[] = (string) $this->element['name'];
      $this->id = implode('_', $parts);
    }
    
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
    
    $this->fieldname = $fieldname;

    $this->name = '';
    if ( $control = $this->form->getInputControlName() ){
      $this->name .= $control;
    }
    
    if ( $this->group ){
      // If we already have a name segment add the group control as another level.
      $groups = explode('.', $this->group);
      if ( '' !== $this->name ){
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
   * Render the field
   * 
   * @param   string  $indent  HTML indent
   * @return   string  HTML
   */
  protected function renderField(array $attrs=[])
  {
    $attrs['fieldname'] = $this->fieldname;
    $attrs['goeswith']  = $this->goeswith;
    $attrs['gonewith']  = $this->gonewith;
    return $attrs;
  }
}
