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
class TabsRenderer extends Renderer
{
  protected $type = 'tabs';
  
  /**
   * {@inheritDoc}
   */
  public function render(array $data=[])
  {
    $fieldsets = $this->getFieldsets();
    
    $_tabs = [];
    $i=0;
    foreach($fieldsets as $fieldset){
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
      
      if ( empty($fieldset['label']) ){
        $fieldset['label'] = '';
      }
      
      if ( empty($fieldset['description']) ){
        $fieldset['description'] = '';
      }
      
      $fieldset['component']   = $this->form->getComponent();
      $fieldset['group']       = $group;
      $fieldset['active']      = ( $i === 0 );
      $fieldset['name']        = $name;
      $fieldset['label']       = FormHelper::getFieldsetLabel($fieldset['label'], $this->form->getComponent(), $fieldset['name']);
      $fieldset['description'] = FormHelper::getFieldsetDescription($fieldset['description'], $this->form->getComponent(), $fieldset['name']);
      
      $_tabs[] = new Fieldset($fieldset);
      
      $i++;
    }
    
    $tabs     = [];
    $contents = [];
    
    foreach($_tabs as $tab){
      if ( !$tab->getFields() ){
        continue;
      }
      
      $tabs[] = [
        'id' => $tab->getId(),
        'active' => $tab->isActive(),
        'legend' => $tab->getLegend(),
      ];
      
      $contents[] = [
        'id' => $tab->getId(),
        'active' => $tab->isActive(),
        'description' => $tab->getDescription(),
        'fields' => $tab->getFields(),
      ];
    }
    
    return parent::render(array_merge($data, [
      'tabs' => $tabs,
      'contents' => $contents,
    ]));
  }
}