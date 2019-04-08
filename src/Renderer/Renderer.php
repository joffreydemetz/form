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
  public function addHiddenField(string $name, $value)
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
  public function setButtons(array $buttons)
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
  public function render(array $data=[]): array
  {
    $data['renderer']    = $this->type;
    $data['buttons']     = $this->buttons;
    $data['buttongroup'] = count($this->buttons) > 1;
    $data['offset']      = $this->getBootstrapInputOffset();
    $data['component']   = $this->form->getComponent();
    $data['hidden']      = $this->hiddenFields;
    return $data;
  }
  
  /**
   * Render a fieldset
   * 
   * @param  string  $name  Fieldset name
   * @return string  HTML
   */
  public function getFields($fieldset): array
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
  public function getBootstrapInputOffset(): int
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
  
  protected function getFieldsets(): array
  {
    $fieldsets = [];
    
    if ( $this->fieldsetOrdering ){
      foreach($this->fieldsetOrdering as $fieldset){
        $fieldsets[$fieldset] = null;
      }
    }
    
    foreach($this->form->getFieldsets() as $name => $fieldset){
      if ( $fields = $this->getFields($name) ){
        foreach($fields as &$field){
          if ( $field['type'] === 'inputgroup' ){
            if ( !Inputgroup::create($field['inputgroupId']) ){
              throw new RuntimeException('Inputgroup was not created properly');
            }
            $inputgroup = Inputgroup::create($field['inputgroupId']);
            $field = $inputgroup->render();
          }
        }
        
        $fieldset = (array)$fieldset;
        $fieldset['fields'] = $fields;
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
   * Render form field.
   * 
   * @param  Field   $field  Field object
   */
  protected function field(Field $field): array
  {
    $fieldData = [];
    
    $field->cleanForRender();
    
    if ( $field->isHiddenWhenEmpty() && $field->isEmpty() ){
      return $fieldData;
    }
    
    if ( !$field->isStatic() && $field->isHidden() ){
      $this->addHiddenField($field->getName(), $field->getValue());
      return $fieldData;
    }
    
    $contained = ( $this->form->isHorizontal() );
    
    if ( $field->isStatic() ){
      $fieldData = $this->fieldStatic($field);
    } 
    elseif ( $inputgroupId = $field->getInputgroupId() ){
      $newInputgroup = false === Inputgroup::exists($inputgroupId);
      
      $inputgroup = Inputgroup::create($inputgroupId);
      if ( $newInputgroup ){
        $inputgroup
          ->setFieldname($field->getFieldname())
          ->setClassname(implode(' ', $field->getFieldClasses()))
          ->setLabel($this->fieldLabel($field))
          ->setTip($field->getTip())
          ->setTipBefore($field->getTipBefore());
      }
      
      if ( $buttonBefore = $field->getButtonBefore() ){
        $buttonBeforeClass = $field->getButtonBeforeClass();
        $inputgroup->addButton($buttonBefore, $buttonBeforeClass);
      }
      
      if ( $spanBefore = $field->getSpanBefore() ){
        $spanBeforeClass = $field->getSpanBeforeClass();
        $inputgroup->addButtonShell($spanBefore, $spanBeforeClass);
      }
      
      $inputgroup->addField($field->getFieldHtml());
      
      if ( $spanAfter = $field->getSpanAfter() ){
        $spanAfterClass = $field->getSpanAfterClass();
        $inputgroup->addButtonShell($spanAfter, $spanAfterClass);
      }
      
      if ( $buttonAfter = $field->getButtonAfter() ){
        $buttonAfterClass = $field->getButtonAfterClass();
        $inputgroup->addButton($buttonAfter, $buttonAfterClass);
      }
      
      $fieldData = [
        'type' => 'inputgroup',
        'inputgroupId' => $inputgroupId,
      ];
      
      if ( !$newInputgroup ){
        return [];
      }
    }
    // elseif ( $field->getSpanBefore() || $field->getSpanAfter() || $field->getInputgroupId() ){
      // $fieldData = $this->fieldInputGroup($field);
    // }
    else {
      $fieldData = $field->getFieldHtml();
    }
    
    if ( true === $contained ){
      $classes = $field->getContainerClasses();
      
      $attrs = [];
      
      if ( $this->form->isHorizontal() ){
        $_classes = explode(' ', $this->bootstrapInputClass);
        
        if ( $field->isLabelHidden() ){
          foreach($_classes as $i => $v){
            if ( substr($v, 0, 4) === 'col-' ){
              unset($_classes[$i]);
              continue;
            }
          }
          
          if ( $field->isFullWidth() ){
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
    
    if ( !isset($fieldData['label']) ){
      $fieldData['label'] = $this->fieldLabel($field);
    }
    
    if ( !isset($fieldData['tip']) ){
      $fieldData['tip'] = '';
    }
    
    return $fieldData;
  }
  
  /**
   * Render a form field label.
   * 
   * @param  Field   $field  Field object
   * @return string  HTML 
   */
  protected function fieldLabel(Field $field): array
  {
    $labelData = [];
    
    if ( !$field->isHidden() && !$field->isLabelHidden() ){
      $element   = $field->getElement();
      $classes   = $field->getLabelClasses();
      $labelText = $field->getLabelText();
      
      array_unshift($classes, 'control-label');
      
      $labelData['attrs'] = [];
      $labelData['attrs']['for']   = $field->getId();
      $labelData['attrs']['class'] = implode(' ', $classes);
      
      if ( $field->isLabelSrOnly() ){
        array_unshift($classes, 'sr-only');
      }
      elseif ( $this->form->isHorizontal() ){
        $_classes = explode(' ', $this->bootstrapLabelClass);
        $_classes[] = 'control-label';
        $classes = array_merge($_classes, $classes);
      }
      
      $labelData['attrs']['class'] = implode(' ', $classes);
      
      $fieldName = (string) $element['name'];
      $labelData['text'] = $field->getLabelText();
      
      if ( '' === $labelData['text'] ){
        $labelData['text'] = 'FIELD_'.$this->form->getComponent().'_'.$fieldName.'_LABEL';
      }
    }
    
    return $labelData;
  }
  
  /**
   * Render a form field as an hidden input.
   * 
   * @param  Field   $field  Field object
   * @return string  HTML 
   */
  protected function fieldHiddenInput(Field $field): array
  {
    $attrs['type']  = 'hidden';
    $attrs['name']  = $field->getName();
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
  /* protected function fieldInputGroup(Field $field): array
  {
    $inputgroupId = $field->getInputgroupId();
    
    return [
      'type'      => $inputgroupId ? 'ig' : 'inputgroup',
      'fieldname' => $field->getFieldname(),
      'goeswith'  => $field->getGoesWith(),
      'gonewith'  => $field->getGoneWith(),
      
      'inputgroupId' => $field->getInputgroupId(),
      'bsClass'   => $field->getInputgroupClass(),
      // 'bsPrefix'  => $field->getSpanBefore(),
      // 'bsSuffix'  => $field->getSpanAfter(),
      'field'     => $field->getFieldHtml(),
    ];
  } */
  
  /**
   * Render a form field as static
   * 
   * @param  Field   $field  Field object
   * @return string  HTML
   */
  protected function fieldStatic(Field $field): array
  {
    return [
      'type'     => 'static',
      'goeswith' => $field->getGoesWith(),
      'gonewith' => $field->getGoneWith(),
      'value'    => $field->getStaticValue(),
    ];
  }
}