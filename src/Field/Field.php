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
   * Field validation filter
   * 
   * @var    string   
   */
  protected $filter;
  
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
   * Field width attribute
   * 
   * @var    int   
   */
  protected $width;
  
  /**
   * Field class attribute
   * 
   * @var    string   
   */
  protected $class;
  
  /**
   * Field style attribute
   * 
   * @var    string   
   */
  protected $style;
  
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
   * The field tip
   * 
   * @var    string
   */
  protected $tip;
  
  /**
   * The tip goes before the tip
   * 
   * @var    bool
   */
  protected $tipBefore;
  
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
   * Field autocomplete attribute
   * 
   * @var    string   
   */
  protected $autocomplete;
  
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
   * Label sr only
   * 
   * @var    bool
   */
  protected $labelSrOnly;
  
  /**
   * Hide field label
   * 
   * @var    bool   
   */
  protected $labelHide;
  
  /**
   * Inputgroup Type
   * 
   * @var    string   
   */
  protected $inputgroupId;
  
  /**
   * Inputgroup class
   * 
   * @var    string   
   */
  protected $inputgroupClass;
  
  /**
   * Inputgroup prefix span
   * 
   * @var    string   
   */
  protected $spanBefore;
  
  /**
   * Inputgroup prefix span class
   * 
   * @var    string   
   */
  protected $spanBeforeClass;
  
  /**
   * Inputgroup suffix span
   * 
   * @var    string   
   */
  protected $spanAfter;
  
  /**
   * Inputgroup suffix span class
   * 
   * @var    string   
   */
  protected $spanAfterClass;
  
  /**
   * Inputgroup prefix button
   * 
   * @var    string   
   */
  protected $buttonBefore;
  
  /**
   * Inputgroup prefix button class
   * 
   * @var    string   
   */
  protected $buttonBeforeClass;
  
  /**
   * Inputgroup suffix button
   * 
   * @var    string   
   */
  protected $buttonAfter;
  
  /**
   * Inputgroup suffix button class
   * 
   * @var    string   
   */
  protected $buttonAfterClass;
  
  public static function create($type)
  {
    if ( !$type ){
      $element['type'] = 'text';
    }
    
    if ( $type === 'datetime-local' ){
      $type = 'datetime';
    }
    
    $type = str_replace('-', '', $type);
    
    foreach(Form::$ns as $ns){
      $Class = $ns.'\\Field\\'.ucfirst($type);
      if ( class_exists($Class) ){
        break;
      }
    }
    
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
  
  public function get($property, $default=null)
  {
    if ( isset($this->{$property}) ){
      return $this->{$property};
    }
    return $default;
  }
  
  public function set($property, $value = null)
  {
    $previous = isset($this->$property) ? $this->$property : null;
    $this->$property = $value;
    return $previous;
  }
  
  public function has($property): bool
  {
    return ( isset($this->{$property}) );
  }
  
  public function all(): array
  {
    return get_object_vars($this);
  }

  public function erase($property)
  {
    if ( isset($this->$property) ){
      unset($this->$property);
    }
  }
  
  public function sets(array $properties)
  {
    foreach($properties as $k => $v){
      $this->set($k, $v);
    }
    
    return $this;
  }

  public function setForm(Form $form)
  {
    $this->form = $form;
    return $this;
  }
  
  public function setElement(SimpleXMLElement $element)
  {
    if ( empty($element['name']) ){
      throw new RuntimeException('Missing field name in XML definition');
    }
    
    $this->element = $element;
    return $this;
  }
  
  public function setGroup($group=null)
  {
    $this->group = $group;
    return $this;
  }
  
  public function setValue($value=null)
  {
    $this->value = $value === null ? '' : $value;
    
    $this->checkValue();
    return $this;
  }
  
  /* public function setAttribute(string $attribute, $value='', $type='string')
  {
    $this->element[$attribute] = $value;
    return $this;
  }
  
  public function defAttribute(string $attribute, $default=null, $type='string')
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
    
    $this->setAttribute($attribute, $value);
    return $this;
  } */
  
  public function setAttribute(string $attribute, string $value='')
  {
    $this->element[$attribute] = $value;
    return $this;
  }
  
  public function defAttribute(string $attribute, string $default='')
  {
    $attrs = $this->element->attributes();
    
    if ( !isset($attrs[$attribute]) ){
      $this->setAttribute($attribute, $default);
    }
    
    return $this;
  }
  
  
  /**
   * @deprecated
   */
  public function setProperties($properties)
  {
    $this->sets((array)$properties);
    return $this;
  }
  
  /**
   * @deprecated
   */
  public function export(): array
  {
    return $this->all();
  }
  
  /**
   * @deprecated
   */
  public function getProperties(): array
  {
    return $this->all();
  }
  
  public function getForm(): Form
  {
    return $this->form;
  }
  
  public function getElement(): SimpleXMLElement
  {
    return $this->element;
  }
  
  public function getFieldname(): string
  {
    return $this->fieldname;
  }
  
  public function getGroup(): string
  {
    return $this->group;
  }
  
  public function getValue(): string
  {
    return $this->value;
  }
  
  public function getId(): string
  {
    return $this->id;
  }
  
  public function getName(): string
  {
    return $this->name;
  }
  
  public function getFilter(): string
  {
    return $this->filter;
  }
  
  public function getGoesWith(): string
  {
    return $this->goeswith;
  }
  
  public function getGoneWith(): string
  {
    return $this->gonewith;
  }
  
  public function getWidth(): string
  {
    return $this->width;
  }
  
  public function getClass(): string
  {
    return $this->class;
  }
  
  public function getStyle(): string
  {
    return $this->style;
  }
  
  public function getDefault(): string
  {
    return $this->default;
  }
  
  public function getDescription(): string
  {
    return $this->description;
  }
  
  public function getContainerClass(): string
  {
    return $this->containerClass;
  }
  
  public function getLabelClass(): string
  {
    return $this->labelClass;
  }
  
  public function getLabelText(): string
  {
    return $this->labelText;
  }
  
  public function getTip(): string
  {
    return $this->tip;
  }
  
  public function getTipBefore(): string
  {
    return $this->tipBefore;
  }
  
  public function getValidate(): string
  {
    return $this->validate;
  }
  
  public function getMessage(): string
  {
    return $this->message;
  }
  
  public function getAutocomplete(): string
  {
    return $this->autocomplete;
  }

  public function getInputgroupId(): string
  {
    return $this->inputgroupId;
  }
  
  public function getInputgroupClass(): string
  {
    return $this->inputgroupClass;
  }
  
  public function getSpanBefore(): string
  {
    return $this->spanBefore;
  }
  
  public function getSpanBeforeClass(): string
  {
    return $this->spanBeforeClass;
  }
  
  public function getSpanAfter(): string
  {
    return $this->spanAfter;
  }
  
  public function getSpanAfterClass(): string
  {
    return $this->spanAfterClass;
  }
  
  public function getButtonBefore(): string
  {
    return $this->buttonBefore;
  }
  
  public function getButtonBeforeClass(): string
  {
    return $this->buttonBeforeClass;
  }
  
  public function getButtonAfter(): string
  {
    return $this->buttonAfter;
  }
  
  public function getButtonAfterClass(): string
  {
    return $this->buttonAfterClass;
  }
  
  public function isStatic(): bool
  {
    return true == $this->static;
  }
  
  public function isHidden(): bool
  {
    return true == $this->hidden;
  }
  
  public function isRequired(): bool
  {
    return true == $this->required;
  }
  
  public function isReadonly(): bool
  {
    return true == $this->readonly;
  }
  
  public function isDisabled(): bool
  {
    return true == $this->disabled;
  }
  
  public function isMultiple(): bool
  {
    return true == $this->multiple;
  }
  
  public function isTipBefore(): bool
  {
    return true === $this->tipBefore;
  }
  
  public function isFullWidth(): bool
  {
    return true == $this->fullWidth;
  }
  
  public function isAutofocus(): bool
  {
    return true == $this->autofocus;
  }
  
  public function isHiddenWhenEmpty(): bool
  {
    return true == $this->hideWhenEmpty;
  }
  
  public function isCanBeStatic(): bool
  {
    return true == $this->canBeStatic;
  }
  
  public function isLabelSrOnly(): bool
  {
    return true == $this->labelSrOnly;
  }
  
  public function isLabelHidden(): bool
  {
    return true == $this->labelHide;
  }
  
  public function isEmpty(): bool
  {
    return '' === $this->value;
  }
  
  public function getLabelClasses(): array
  {
    $classes = [];
    
    if ( !$this->labelHide ){
      if ( $this->labelClass !== '' ){
        $_classes = explode(' ', $this->labelClass);
        $classes = array_merge($classes, $_classes);
        $classes = array_unique($classes);
      }
      
      if ( true === $this->required ){
        $classes[] = 'required';
      }      
    }
    
    return $classes;
  }
  
  public function getFieldClasses(): array
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
    
    if ( true === $this->fullWidth ){
      $classes[] = 'fullwidth';
    }
    
    return $classes;
  }
  
  public function getFieldHtml(array $attrs=[]): array
  {
    return $this->renderField($attrs);
  }
  
  public function getStaticValue(): string
  {
    return FormHelper::formatStaticValue($this->value);
  }
  
  public function getHiddenValue(): string
  {
    return FormHelper::formatHiddenValue($this->value);
  }
  
  public function getFieldAttributes(array $attrs=[]): array
  {
    $classes = $this->getFieldClasses();
    
    $attrs['data-id'] = $this->id;
    $attrs['name']    = $this->name;
     
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
    
    if ( $this->width ){
      $attrs['width'] = $this->width;
    }
    
    if ( $this->style ){
      $attrs['style'] = $this->style;
    }
    
    // if ( $this->spanBefore ){
      // $attrs['aria-describedby'] = 'bsigroup-'.$this->id;
    // }
    
    $attrs['class'] = implode(' ', $classes);
    return $attrs;
  }
  
  public function getContainerClasses(): array
  {
    $classes = [];
    
    if ( $this->containerClass !== '' ){
      $_classes = explode(' ', $this->containerClass);
      $classes = array_merge($classes, $_classes);
    }
    
    return $classes;
  }
  
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
  
  public function cleanForRender()
  {
    if ( true === $this->readonly ){
      $this->required = false;
    }
    
    $this->checkValue();
    $this->checkValidate();
    $this->checkState();
    
    return $this;
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
    $this->defAttribute('required', 'false');
    $this->defAttribute('readonly', 'false');
    $this->defAttribute('disabled', 'false');
    $this->defAttribute('autofocus', 'false');
    $this->defAttribute('hidden', 'false');
    $this->defAttribute('hideWhenEmpty', 'false');
    $this->defAttribute('labelHide', 'false');
    $this->defAttribute('labelSrOnly', 'false');
    $this->defAttribute('canBeStatic', 'false');
    $this->defAttribute('fullWidth', 'false');
    $this->defAttribute('tipBefore', 'false');
    
    $this->defAttribute('width', '0');
    
    $this->defAttribute('default', '');
    $this->defAttribute('description', '');
    $this->defAttribute('filter', 'raw');
    $this->defAttribute('class', '');
    $this->defAttribute('style', '');
    $this->defAttribute('containerClass', '');
    $this->defAttribute('labelClass', '');
    $this->defAttribute('labelText', '');
    $this->defAttribute('validate', '');
    $this->defAttribute('message', '');
    $this->defAttribute('autocomplete', '');
    $this->defAttribute('goeswith', '');
    $this->defAttribute('gonewith', '');
    $this->defAttribute('tip', '');
    
    // inputgroup
    $this->defAttribute('inputgroupId', '');
    $this->defAttribute('inputgroupClass', '');
    $this->defAttribute('spanBefore', '');
    $this->defAttribute('spanBeforeClass', '');
    $this->defAttribute('spanAfter', '');
    $this->defAttribute('spanAfterClass', '');
    $this->defAttribute('buttonBefore', '');
    $this->defAttribute('buttonBeforeClass', '');
    $this->defAttribute('buttonAfter', '');
    $this->defAttribute('buttonAfterClass', '');
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
    $this->labelSrOnly    = ( (string) $this->element['labelSrOnly'] === 'true' );
    $this->fullWidth      = ( (string) $this->element['fullWidth'] === 'true' );
    $this->tipBefore      = ( (string) $this->element['tipBefore'] === 'true' );
    
    $this->default        = (string) $this->element['default'];
    $this->description    = (string) $this->element['description'];
    $this->filter         = (string) $this->element['filter'];
    $this->validate       = (string) $this->element['validate'];
    $this->class          = (string) $this->element['class'];
    $this->style          = (string) $this->element['style'];
    $this->containerClass = (string) $this->element['containerClass'];
    $this->labelClass     = (string) $this->element['labelClass'];
    $this->labelText      = (string) $this->element['labelText'];
    $this->tip            = (string) $this->element['tip'];
    $this->autocomplete   = (string) $this->element['autocomplete'];
    
    $this->width          = (int) $this->element['width'];
    
    // inputgroup
    $this->inputgroupId      = (string) $this->element['inputgroupId'];
    $this->inputgroupClass   = (string) $this->element['inputgroupClass'];
    $this->spanBefore        = (string) $this->element['spanBefore'];
    $this->spanBeforeClass   = (string) $this->element['spanBeforeClass'];
    $this->spanAfter         = (string) $this->element['spanAfter'];
    $this->spanAfterClass    = (string) $this->element['spanAfterClass'];
    $this->buttonBefore      = (string) $this->element['buttonBefore'];
    $this->buttonBeforeClass = (string) $this->element['buttonBeforeClass'];
    $this->buttonAfter       = (string) $this->element['buttonAfter'];
    $this->buttonAfterClass  = (string) $this->element['buttonAfterClass'];
    //
    $this->goeswith = (string) $this->element['goeswith'];
    $this->gonewith = (string) $this->element['gonewith'];
    
    if ( $this->fullWidth ){
      $this->labelHide  = true;
      $this->labelText  = '';
      $this->labelClass = '';
    }
  }
  
  /**
   * Check the field value
   *
   * @return   void
   */
  protected function checkValue()
  {
    if ( $this->filter === 'int' ){
      // if ( $this->value !== '' ){
        if ( intval($this->value) === 0 ){
          $this->value = $this->default;
        }
      // }
      
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
    if ( $this->isCanBeStatic() ){
      if ( $this->isReadonly() ){
        if ( $this->isHiddenWhenEmpty() && $this->isEmpty() ){
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
      
      // $parts[] = $this->form->getComponent();
      $parts[] = strtolower($this->form->getName());
      
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
  protected function renderField(array $attrs=[]): array
  {
    $attrs['fieldname']    = $this->fieldname;
    $attrs['goeswith']     = $this->goeswith;
    $attrs['gonewith']     = $this->gonewith;
    $attrs['tip']          = $this->tip;
    $attrs['tipBefore']    = $this->tipBefore;
    $attrs['inputgroupId'] = $this->inputgroupId;
    return $attrs;
  }
}
