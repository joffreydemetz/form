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
    $preprendTabs = [];
    $appendTabs   = [];
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
      
      // if ( $name === 'main' ){
        // $preprendTabs[] = new Fieldset($fieldset);
      // }
      // elseif ( $name === 'infos' ){
        // $appendTabs[] = new Fieldset($fieldset);
      // }
      // else {
        $_tabs[] = new Fieldset($fieldset);
      // }
      
      $i++;
    }
    
    // foreach($preprendTabs as $_tab){
      // array_unshift($_tabs, $_tab);
    // }
    
    // foreach($appendTabs as $_tab){
      // $_tabs[] = $_tab;
    // }
    
    $tabs     = [];
    $contents = [];
    
    foreach($_tabs as $tab){
      if ( !$tab->get('fields') ){
        continue;
      }
      
      $tabs[] = [
        'id' => $tab->get('id'),
        'active' => $tab->get('active'),
        'legend' => $tab->get('legend'),
      ];
      
      $contents[] = [
        'id' => $tab->get('id'),
        'active' => $tab->get('active'),
        'description' => $tab->get('description'),
        'fields' => $tab->get('fields'),
      ];
    }
    
    return parent::render(array_merge($data, [
      'tabs' => $tabs,
      'contents' => $contents,
    ]));
  }
}