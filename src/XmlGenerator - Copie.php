<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form;

use JDZ\Utilities\Xml as XmlObject;
use Exception;
use RuntimeException;

/**
 * Generate Xml Form
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class XmlGenerator 
{
  protected $subForm = false;
  
  public function __construct()
  {
    if ( !$this->subForm ){
      $this->fieldsets = [];
      $this->merge( $this->data() );
    }
  }
  
  public function getData()
  {
    return $this->data();
  }
  
  public function getXml()
  {
    if ( $this->subForm ){
      throw new RuntimeException('Cannot get XML for a subform in '.get_called_class());
    }
    
    $xmlStr = $this->toString();
    
    try {
      $xml = XmlObject::populateXml($xmlStr, true);
    }
    catch(Exception $e){
      throw new RuntimeException('Invalid Form XML content');
    }
    
    return $xml;
  }
  
  public function merge($data)
  {
    if ( $this->subForm ){
      throw new RuntimeException('Cannot merge to subform in '.get_called_class());
    }
    
    foreach($data as $fieldset => $fields){
      $this->addFieldset($fieldset, $fields);
    }
  }
  
  public function addFieldset($name, $fields)
  {
    if ( !isset($this->fieldsets[$name]) ){
      $this->fieldsets[$name] = [];
    }
    
    foreach($fields as $field){
      $this->addField($field, $name);
    }
  }
  
  public function addField($data, $fieldset='main')
  {
    if ( !isset($this->fieldsets[$fieldset]) ){
      $this->fieldsets[$fieldset] = [];
    }
    
    foreach($this->fieldsets as $_fieldset => &$_fields){
      foreach($_fields as &$_field){
        if ( $_field['name'] === $data['name'] ){
          $_field = array_merge($_field, $data);
          return;
        }
      }
    }
    
    $this->fieldsets[$fieldset][] = $data;
  }
  
  protected function toString()
  {
    $content = [];
    
    $content[] = '<?xml version="1.0" encoding="utf-8"?>';
    $content[] = '<form>';
    
    foreach($this->fieldsets as $fieldset => $fields){
      $content[] = "\t".'<fieldset name="'.$fieldset.'">';
      
      foreach($fields as $field){
        $type    = null;
        $name    = null;
        $options = [];
        $attrs   = [];
        
        if ( isset($field['name']) ){
          $name = $field['name'];
          unset($field['name']);
        }
        else {
          throw new RuntimeException('Missing field name in ' . get_called_class());
        }
        
        if ( isset($field['type']) ){
          $type = $field['type'];
          unset($field['type']);
        }
        else {
          $type = 'text';
        }
        
        if ( isset($field['options']) ){
          $options = $field['options'];
          unset($field['options']);
        }
        
        if ( isset($field['optionsCaller']) ){
          $caller = [ $field['optionsCaller']['class'], $field['optionsCaller']['method'] ];
          if ( is_callable($caller)){
            if ( $_options = call_user_func($caller, $field['optionsCaller']['params']) ){
              $options = array_merge($options, $_options);
            }
          }
          unset($field['optionsCaller']);
        }
        
        $content[] = "\t\t".'<field type="'.$type.'" name="'.$name.'"';
        
        foreach($field as $key => $value){
          $content[] = "\t\t\t".$key.'="'.$value.'"';
        }
        
        if ( $options ){
          $content[] = "\t\t".'>';
          
          foreach($options as $option){
            $content[] = "\t\t\t".'<option value="'.$option['value'].'">'.$option['text'].'</option>';
          }
          
          $content[] = "\t\t".'</field>';
        }
        else {
          $content[] = "\t\t".'/>';
        }
      }
      
      $content[] = "\t".'</fieldset>';
    }
    
    $content[] = '</form>';
    
    return implode("\n", $content);
  }
  
  abstract protected function data();
}