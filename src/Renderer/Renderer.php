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
   * Constructor
   * 
   * @param   Form      $form       Form instance
   * @param   string    $indent     HTML indent
   */
  public function __construct(Form &$form)
  {
    $this->setForm($form);
  }
  
  /**
   * Set the form singleton
   * 
   * @param   Form      $form       Form instance
   * @return   void
   */
  public function setForm(Form &$form)
  {
    $this->form =& $form;
  }
  
  /**
   * Render tabs
   * 
   * @param   string    $name             Fieldset name
   * @return   string    HTML
   */
  public function tabs($name)
  {
    $form_fieldsets = $this->form->getFieldsets();
    
    $_fieldsets = [];
    foreach($form_fieldsets as $form_fieldset){
      $_fieldsets[] = (array)$form_fieldset;
    }
    
    $_tabs = [];
    $preprendTabs = [];
    $appendTabs   = [];
    foreach($_fieldsets as $i => $_fieldset){
      $_fieldset = (array)$_fieldset;
      $_name     = $_fieldset['name'];
      
      if ( preg_match("/^(.+)\.(.+)$/", $_name, $m) ){
        $group = $m[1];
        $name  = $m[2];
      }
      else {
        $group = '';
        $name  = $_name;
      }
      
      if ( empty($_fieldset['label']) ){
        $_fieldset['label'] = '';
      }
      
      if ( empty($_fieldset['description']) ){
        $_fieldset['description'] = '';
      }
      
      $_fieldset['controller']  = $this->form->getContext();
      $_fieldset['group']       = $group;
      $_fieldset['active']      = ( $i === 0 );
      $_fieldset['name']        = $name;
      $_fieldset['fields']      = $this->form->getFieldset($_name);
      $_fieldset['label']       = FormHelper::getFieldsetLabel($_fieldset['label'], $_fieldset['controller'], $_fieldset['name']);
      $_fieldset['description'] = FormHelper::getFieldsetDescription($_fieldset['description'], $_fieldset['controller'], $_fieldset['name']);
      
      if ( $name === 'main' ){
        $preprendTabs[] = new Fieldset($_fieldset);
      }
      elseif ( $name === 'infos' ){
        $appendTabs[] = new Fieldset($_fieldset);
      }
      else {
        $_tabs[] = new Fieldset($_fieldset);
      }
    }
    
    foreach($preprendTabs as $_tab){
      array_unshift($_tabs, $_tab);
    }
    
    foreach($appendTabs as $_tab){
      array_push($_tabs, $_tab);
    }
    
    $tabs     = [];
    $contents = [];
    foreach($_tabs as $_tab){
      if ( $tab = $this->tabContent($_tab) ){
        $tabs[]     = $this->tabTab($_tab);
        $contents[] = $tab;
      }
    }
    
    return [
      'tabs' => $tabs,
      'contents' => $contents,
    ];
  }
  
  /**
   * Render a fieldset
   * 
   * @param   string    $name             Fieldset name
   * @return   string    HTML
   */
  public function fieldsets(array $names)
  {
    $fieldsets = [];
    foreach($names as $name){
      $fieldsets[] = $this->fieldset($name);
    }
    
    return $fieldsets;
  }
  
  /**
   * Render a fieldset
   * 
   * @param   string    $name             Fieldset name
   * @return   string    HTML
   */
  public function fieldset($name)
  {
    $form_fieldset_fields = $this->form->getFieldset($name);
    
    $fields = [];
    foreach($form_fieldset_fields as $field){
      if ( $_field = $this->field($field) ){
        $fields[] = $_field;
      }
    }
    
    return [
      'name' => $name,
      'fields' => $fields,
    ];
  }
  
  /**
   * Render form buttons
   *
   * @param   string    $itemController   The item task controller
   * @return   string  HTML 
   */
  public function buttons($itemController)
  {
    $buttons = [];
    
    if ( $actions = $this->form->getOption('buttons') ){
      $actions = explode(',', $actions);
      
      foreach($actions as $task){
        switch($task){
          case 'cancel':
            $class = 'danger';
            if ( $this->form->getFormOption('update') === false ){
              $text = 'CLOSE';
            }
            else {
              $text = 'CANCEL';
            }
            break;
          
          case 'save':
            $class = 'success';
            $text  = 'SAVE_AND_CLOSE';
            break;
          
          case 'apply':
            $class = 'warning';
            $text  = 'APPLY';
            break;
          
          default:
            $class = 'info';
            $text  = strtoupper($task);
            break;
        }
        
        $buttons[] = [
          'class' => 'btn btn-'.$class,
          'task' => $itemController.'.'.$task,
          'text' => $text,
        ];
      }
    }
    
    return $buttons;
  }
  
  /**
   * Render form accordion
   * 
   * @param   string    $name             Fieldset name
   * @return   string    HTML
   * @deprecated
   */
  public function accordion($name)
  {
    $form_fieldsets = $this->form->getFieldsets();
    
    $_fieldsets = [];
    foreach($form_fieldsets as $form_fieldset){
      $form_fieldset = (array)$form_fieldset;
      
      if ( !preg_match("/^".$name."\.(.+)$/", $form_fieldset['name']) ){
        continue;
      }
      
      $_fieldsets[] = $form_fieldset;
    }
    
    $_panels = [];
    foreach($_fieldsets as $i => $_fieldset){
      $_fieldset = (array)$_fieldset;
      $_name     = $_fieldset['name'];
      
      list($group, $name) = explode('.', $_name);
      
      if ( empty($_fieldset['label']) ){
        $_fieldset['label'] = '';
      }
      
      if ( empty($_fieldset['description']) ){
        $_fieldset['description'] = '';
      }
      
      $_fieldset['controller']  = $this->form->getContext();
      $_fieldset['group']       = $group;
      $_fieldset['active']      = ( $i === 0 );
      $_fieldset['name']        = $name;
      $_fieldset['fields']      = $this->form->getFieldset($_name);
      $_fieldset['label']       = FormHelper::getFieldsetLabel($_fieldset['label'], $_fieldset['controller'], $_fieldset['name']);
      $_fieldset['description'] = FormHelper::getFieldsetDescription($_fieldset['description'], $_fieldset['controller'], $_fieldset['name']);
      
      $_panels[] = new Fieldset($_fieldset);
    }
    
    $panels = [];
    foreach($_panels as $panel){
      if ( $panel = $this->panel($panel) ){
        $panels[] = $panel;
      }
    }
    
    if ( empty($panels) ){
      return false;
    }
    
    return [
      'id' => $group,
      'panels' => $panels,
    ];
  }
  
  /**
   * Render panel
   * 
   * @param   Fieldset   $tab  Panel object
   * @return   string  HTML 
   * @deprecated
   */
  protected function tabTab(Fieldset $tab)
  {
    return [
      'id' => $tab->get('id'),
      'active' => $tab->get('active'),
      'legend' => $tab->get('legend'),
    ];
    
    return (string) $html;
  }
  
  /**
   * Render tab fieldset
   * 
   * @param   Field   $field  Field object
   * @return   string  HTML 
   */
  protected function tabContent(Fieldset $tab)
  {
    $fields = [];
    foreach((array)$tab->get('fields') as $field){
      if ( $_field = $this->field($field) ){
        $fields[] = $_field;
      }
    }
    
    if ( empty($fields) ){
      return false;
    }
    
    return [
      'id' => $tab->get('id'),
      'active' => $tab->get('active'),
      'description' => $tab->get('description'),
      'fields' => $fields,
    ];
  }
  
  /**
   * Render form field.
   * 
   * @param   Field   $field  Field object
   * @return   string  HTML 
   */
  protected function field(Field $field)
  {
    $field->cleanForRender();
    
    // if ( $field->get('hidden') ){
      // return false;
    // }
    
    if ( $field->isHidden() ){
      return $this->fieldHiddenInput($field);
    }
    
    $contained = ( $field->get('form')->getOption('type') === 'horizontal' );
    
    if ( $field->get('static') ){
      $fieldData = $this->fieldStatic($field);
    } 
    elseif ( $field->get('bsInputgroupPrefix') !== '' || $field->get('bsInputgroupSuffix') !== '' ){
      $fieldData = $this->fieldInputGroup($field);
    }
    else {
      $fieldData = $field->getFieldHtml();
    }
    
    if ( $contained === true ){
      $attrs = [];
      $attrs['class'] = $field->getContainerClass();
      
      $fieldData['container'] = $attrs;
    }
    
    $fieldData['label'] = $this->fieldLabel($field);
    $fieldData['tip']   = $this->fieldDescription($field);
    
    return $fieldData;
  }

  /**
   * Render a form field label.
   * 
   * @param   Field   $field  Field object
   * @return   string  HTML 
   */
  protected function fieldLabel(Field $field)
  {
    if ( !$field->get('hidden') && !$field->get('labelHide') ){
      $id = $field->get('id');
      
      $attrs = [];
      $attrs['id']    = $id.'-lbl';
      $attrs['for']   = $id;
      $attrs['class'] = $field->getLabelClass();
      
      $element     = $field->get('element');
      $formContext = $field->get('form')->getContext();
      $fieldName   = (string) $element['name'];
      
      if ( FormHelper::getTranslation('HELP_FIELD_'.$formContext.'_'.$fieldName) ){
        $fieldHelp = strtoupper('HELP_FIELD_'.$formContext.'_'.$fieldName);

        $attrs['data-help-key']  = $fieldHelp;
        $attrs['data-help-type'] = 'field';
      }
      
      if ( $label = FormHelper::getFieldLabel($field->get('labelText'), $formContext, $fieldName) ){
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
   * @param   Field   $field  Field object
   * @return   string  HTML 
   */
  protected function fieldHiddenInput(Field $field)
  {
    // debugMe($field)->end();
    $attrs['name']  = $field->get('name');
    $attrs['type']  = 'hidden';
    $attrs['value'] = $field->getHiddenValue();
    
    return [
      'type'  => 'hidden',
      'attrs' => $attrs,
    ];
  }
  
  /**
   * Format a bootstrap input-group
   * 
   * @param   Field   $field  Field object
   * @return   string  HTML 
   */
  protected function fieldInputGroup(Field $field)
  {
    return [
      'type'     => 'inputgroup',
      'bsClass'  => $field->get('bsInputgroupClass'),
      'bsPrefix' => $field->get('bsInputgroupPrefix'),
      'bsSuffix' => $field->get('bsInputgroupSuffix'),
      'field'    => $field->getFieldHtml([
        'aria-describedby' => 'bsigroup-'.$field->get('id'),
      ]),
    ];
  }
  
  /**
   * Render a form field as static
   * 
   * @param   Field     $field            Field object
   * @return   string    HTML
   */
  protected function fieldStatic(Field $field)
  {
    return [
      'type'  => 'static',
      'value' => $field->getStaticValue(),
    ];
  }
  
  /**
   * Render a form field helper
   * 
   * @param   Field     $field            Field object
   * @return   string    HTML
   */
  protected function fieldDescription(Field $field)
  {
    $element = $field->get('element');
    $ns      = $field->get('form')->getContext();
    $name    = (string) $element['name'];
    $suffix  = $field->get('form')->getFormOption('update') === true ? 'DESC_UPDATE' : 'DESC';
    
    return FormHelper::getFieldDescription($field->get('description'), $ns, $name, $suffix);
  }
  
  /**
   * Render panel
   * 
   * @param   Fieldset  $panel            Panel fields
   * @return   string    HTML
   * @deprecated
   */
  protected function panel(Fieldset $panel)
  {
    $content = [];
    
    foreach((array)$panel->get('fields') as $field){
      if ( $_field = $this->field($field.'    ') ){
        $content[] = $_field;
      }
    }
    
    if ( trim(implode("\n", $content)) === '' ){
      return false;
    }
    
    return [
      'name' => $panel->get('name'),
      'group' => $panel->get('group'),
      'active' => $panel->get('active'),
      'legend' => $panel->get('legend'),
      'description' => $panel->get('description'),
      'content' => $content,
    ];
  }
}