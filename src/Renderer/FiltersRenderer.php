<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Renderer;

use JDZ\Form\FormHelper;
use JDZ\Form\Field\Field;

/**
 * Form Renderer
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class FiltersRenderer extends Renderer
{
  protected $type = 'filters';
  
  public function render(array $data=[])
  {
    $fieldsets = [];
    
    foreach($this->getFieldsets() as $_fieldset){
      $_fieldset = (array)$_fieldset;
      
      if ( empty($_fieldset['fields']) ){
        continue;
      }
      
      $_name = $_fieldset['name'];
      
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
      
      $fieldsets[$name] = [
        'component'   => $this->form->getComponent(),
        'group'       => $group,
        'name'        => $name,
        // 'label'       => FormHelper::getFieldsetLabel($_fieldset['label'], $this->form->getComponent(), $_fieldset['name']),
        // 'description' => FormHelper::getFieldsetDescription($_fieldset['description'], $this->form->getComponent(), $_fieldset['name']),
        'fields'      => $_fieldset['fields'],
      ];
    }
    
    return parent::render(array_merge($data, [
      'fieldsets' => $fieldsets,
    ]));
  }
}