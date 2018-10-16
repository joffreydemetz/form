<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Renderer;

use JDZ\Form\Form;
use JDZ\Form\FormHelper;
use JDZ\Form\Field\Field;
use JDZ\Helpers\AttributesHelper;

/**
 * Form Renderer
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Renderer 
{
  /**
   * Form instance
   * 
   * @var    Form
   */
  protected $form;
  
  /**
   * Renderer type
   * 
   * @var    string
   */
  protected $type;
  
  /**
   * Hidden fields
   * 
   * @var    array
   */
  protected $hiddenFields;
  
  /**
   * Fieldse odering
   * 
   * @var    array
   */
  protected $fieldsetOrdering = null;
  
  /**
   * Form uses buttons
   * 
   * @var    [array]
   */
  protected $buttons = [];
  
  /**
   * Label class
   * 
   * @var    string
   */
  protected $bootstrapLabelClass = 'col-sm-3';
  
  /**
   * Field container class
   * 
   * @var    string
   */
  protected $bootstrapInputClass = 'col-sm-9';
  
  /**
   * Constructor
   */
  public function __construct()
  {
  }
  
  /**
   * Set the form singleton
   * 
   * @param  Form  $form  Form instance
   * @return $this
   */
  public function setForm(Form $form)
  {
    $this->form = $form;
    return $this;
  }
  
  /**
   * Set the fieldset display ordering
   * 
   * @param  array  $order  Fieldsets ordering
   * @return $this
   */
  public function setFieldsetOrder($order)
  {
    $this->fieldsetOrdering = $order;
    return $this;
  }
  
  /**
   * Set the form singleton
   * 
   * @param  Form  $form  Form instance
   * @return $this
   */
  public function addHiddenField($name, $value)
  {
    $this->hiddenFields[] = [ 
      'type' => 'hidden',
      'attrs' => [
        'name' => $name,
        'type' => 'hidden',
        'value' => $value,
      ],
    ];
    return $this;
  }
  
  /**
   * Set form buttons
   * 
   * @return $this
   */
  public function setButtons($buttons)
  {
    $this->buttons = $buttons;
    return $this;
  }
  
  /**
   * Render the form
   *
   * @param  array   $data  Form data
   * @return string  array 
   */
  public function render(array $data=[])
  {
    $data['renderer']    = $this->type;
    $data['buttons']     = $this->buttons;
    $data['buttongroup'] = count($this->buttons) > 1;
    $data['offset']      = $this->getBootstrapInputOffset();
    $data['component']   = $this->form->getComponent();
    $data['hidden']      = $this->hiddenFields;
    return $data;
  }
  
  protected function getFieldsets()
  {
    $fieldsets = [];
    
    if ( $this->fieldsetOrdering ){
      foreach($this->fieldsetOrdering as $fieldset){
        $fieldsets[$fieldset] = null;
      }
    }
    
    foreach($this->form->getFieldsets() as $name => $fieldset){
      if ( $fields = $this->getFields($name) ){
        $fieldset = (array)$fieldset;
        $fieldset['name']         = $name;
        $fieldset['label']        = '';
        $fieldset['description']  = '';
        $fieldset['fields']       = $fields;
        $fieldsets[$name] = $fieldset;
      }
    }
    
    foreach($fieldsets as $name => $fieldset){
      if ( empty($fieldset['fields']) ){
        unset($fieldsets[$name]);
      }
    }
    
    return array_values($fieldsets);
  }
  
  /**
   * Render a fieldset
   * 
   * @param  string  $name  Fieldset name
   * @return string  HTML
   */
  public function getFields($fieldset)
  {
    $fields = [];
    
    foreach($this->form->getFieldset($fieldset) as $field){
      if ( $_field = $this->field($field) ){
        $fields[] = $_field;
      }
    }
    
    return $fields;
  }
  
  /**
   * Get the bootstrap col offset
   * 
   * @return int
   */
  public function getBootstrapInputOffset()
  {
    if ( $this->form->isHorizontal() ){
      $width  = (int) preg_replace("/[^\d]/", "", $this->bootstrapInputClass);
      $offset = 12 - $width;
    }
    else {
      $offset = 0;
    }
    return $offset;
  }
  
  /**
   * Render form field.
   * 
   * @param  Field   $field  Field object
   * @return string  HTML 
   */
  protected function field(Field $field)
  {
    $field->cleanForRender();
    
    if ( $field->get('hideWhenEmpty') && $field->isEmpty() ){
      return false;
    }
    
    if ( !$field->get('static') && $field->isHidden() ){
      $this->addHiddenField($field->getName(), $field->getValue());
      return false;
    }
    
    $contained = ( $this->form->isHorizontal() );
    
    if ( $field->get('static') ){
      $fieldData = $this->fieldStatic($field);
    } 
    elseif ( $field->get('bsInputgroupPrefix') !== '' || $field->get('bsInputgroupSuffix') !== '' ){
      $fieldData = $this->fieldInputGroup($field);
    }
    else {
      $fieldData = $field->getFieldHtml();
    }
    
    if ( true === $contained ){
      $classes = $field->getContainerClasses();
      
      $attrs = [];
      
      if ( $this->form->isHorizontal() ){
        $_classes = explode(' ', $this->bootstrapInputClass);
        
        if ( $field->get('labelHide') ){
          foreach($_classes as $i => $v){
            if ( substr($v, 0, 4) === 'col-' ){
              unset($_classes[$i]);
              continue;
            }
          }
          
          if ( $field->get('fullWidth') ){
            $_classes[] = 'col-xs-12';
          }
          else {
            $_classes[] = 'col-xs-12';
            $_classes[] = 'col-sm-offset-'.$this->getBootstrapInputOffset();
            $_classes[] = 'col-sm-'.preg_replace("/[^\d]/", "", $this->bootstrapInputClass);
          }
        }
        
        $classes = array_merge($_classes, $classes);
        $classes = array_unique($classes);
        
        $attrs['class'] = implode(' ', $classes);
      }
      
      $fieldData['container'] = $attrs;
    }
    
    $fieldData['label'] = $this->fieldLabel($field);
    $fieldData['tip']   = $this->fieldDescription($field);
    
    return $fieldData;
  }
  
  /**
   * Render a form field label.
   * 
   * @param  Field   $field  Field object
   * @return string  HTML 
   */
  protected function fieldLabel(Field $field)
  {
    if ( !$field->get('hidden') && !$field->get('labelHide') ){
      $id = $field->get('id');
      
      $classes = $field->getLabelClasses();
      array_unshift($classes, 'control-label');
      
      $attrs = [];
      // $attrs['data-id'] = $id.'-lbl';
      $attrs['for']     = $id;
      $attrs['class']   = implode(' ', $classes);
      
      if ( $field->get('labelSrOnly') ){
        array_unshift($classes, 'sr-only');
      }
      elseif ( $this->form->isHorizontal() ){
        $_classes = explode(' ', $this->bootstrapLabelClass);
        $_classes[] = 'control-label';
        $classes = array_merge($_classes, $classes);
      }
      
      $attrs['class'] = implode(' ', $classes);
      
      $element   = $field->get('element');
      $fieldName = (string) $element['name'];
      
      if ( FormHelper::getTranslation('HELP_FIELD_'.$this->form->getI18nNamespace().'_'.$fieldName) ){
        $fieldHelp = strtoupper('HELP_FIELD_'.$this->form->getI18nNamespace().'_'.$fieldName);
        
        $attrs['data-help-key']  = $fieldHelp;
        $attrs['data-help-type'] = 'field';
      }
      
      if ( $label = FormHelper::getFieldLabel($field->get('labelText'), $this->form->getI18nNamespace(), $fieldName) ){
        return [
          'attrs' => $attrs,
          'text' => $label,
        ];
      }
    }
    
    return false;
  }
  
  /**
   * Render a form field as an hidden input.
   * 
   * @param  Field   $field  Field object
   * @return string  HTML 
   */
  protected function fieldHiddenInput(Field $field)
  {
    $attrs['name']  = $field->get('name');
    $attrs['type']  = 'hidden';
    $attrs['value'] = $field->getHiddenValue();
    
    return [
      'type'   => 'hidden',
      'attrs'  => $attrs,
    ];
  }
  
  /**
   * Format a bootstrap input-group
   * 
   * @param  Field   $field  Field object
   * @return string  HTML 
   */
  protected function fieldInputGroup(Field $field)
  {
    return [
      'type'      => 'inputgroup',
      'fieldname' => $field->get('fieldname'),
      'goeswith'  => $field->get('goeswith'),
      'gonewith'  => $field->get('gonewith'),
      'bsClass'   => $field->get('bsInputgroupClass'),
      'bsPrefix'  => $field->get('bsInputgroupPrefix'),
      'bsSuffix'  => $field->get('bsInputgroupSuffix'),
      'field'     => $field->getFieldHtml(),
    ];
  }
  
  /**
   * Render a form field as static
   * 
   * @param  Field   $field  Field object
   * @return string  HTML
   */
  protected function fieldStatic(Field $field)
  {
    return [
      'type'     => 'static',
      'goeswith' => $field->get('goeswith'),
      'gonewith' => $field->get('gonewith'),
      'value'    => $field->getStaticValue(),
    ];
  }
  
  /**
   * Render a form field helper
   * 
   * @param  Field   $field  Field object
   * @return string  HTML
   */
  protected function fieldDescription(Field $field)
  {
    $element = $field->get('element');
    $name    = (string) $element['name'];
    
    if ( $this->form->isUpdateMode() ){
      $tip = FormHelper::getFieldDescription($field->get('description'), $this->form->getI18nNamespace(), $name, 'DESC_UPDATE');
      if ( !$tip ){
        $tip = FormHelper::getFieldDescription($field->get('description'), $this->form->getI18nNamespace(), $name, 'DESC');
      }
    }
    else {
      $tip = FormHelper::getFieldDescription($field->get('description'), $this->form->getI18nNamespace(), $name, 'DESC');
    }
    
    return $tip;
  }
}