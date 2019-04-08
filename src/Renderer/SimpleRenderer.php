<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Renderer;

use JDZ\Form\FormHelper;

/**
 * Form Renderer
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class SimpleRenderer extends Renderer
{
  protected $type = 'simple';
  
  public function render(array $data=[]): array
  {
    $fieldsets = $this->getFieldsets();
    
    foreach($fieldsets as $name => &$fieldset){
      $fieldset = (array)$fieldset;
      
      $_name = $fieldset['name'];
      
      if ( preg_match("/^(.+)\.(.+)$/", $_name, $m) ){
        $group = $m[1];
        $name  = $m[2];
      }
      else {
        $group = '';
        $name  = $_name;
      }
      
      if ( !isset($fieldset['label']) ){
        $fieldset['label'] = '';
        $fieldset['label'] = FormHelper::getFieldsetLabel($fieldset['label'], $this->form->getComponent(), trim($group.'_'.$name, '_'));
      }
      
      if ( !isset($fieldset['description']) ){
        $fieldset['description'] = '';
        $fieldset['description'] = FormHelper::getFieldsetDescription($fieldset['description'], $this->form->getComponent(), trim($group.'_'.$name, '_'));
      }
    }
    
    return parent::render(array_merge($data, [
      'fieldsets' => $fieldsets,
    ]));
  }
}