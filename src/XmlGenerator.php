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
  public function __construct()
  {
    $this->fieldsets = [];
    
    foreach($this->data() as $fieldset => $fields){
      $this->addFieldset($fieldset, $fields);
    }
  }
  
  public function __toString()
  {
    return $this->toString();
  }
  
  public function getXml()
  {
    $xmlStr = $this->toString();
    
    try {
      $xml = XmlObject::populateXml($xmlStr, true);
    }
    catch(Exception $e){
      throw new RuntimeException('Invalid Form XML content');
    }
    
    return $xml;
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
        $options = null;
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
