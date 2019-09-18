<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form;

use JDZ\Utilities\Xml as XmlObject;

/**
 * Generate Xml Form
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class XmlGenerator 
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
      throw new \Exception('Cannot get XML for a subform in '.get_called_class());
    }
    
    $xmlStr = $this->toString();
    try {
      $xml = XmlObject::populateXml($xmlStr, true);
    }
    catch(Exception $e){
      throw new \Exception('Invalid Form XML content'.$e->getMessage());
    }
    
    return $xml;
  }
  
  public function merge($data)
  {
    if ( $this->subForm ){
      throw new \Exception('Cannot merge to subform in '.get_called_class());
    }
    
    foreach($data as $fieldset => $fields){
      $this->addFieldset($fieldset, $fields);
    }
  }
  
  public function setFieldsetLabel(string $name, string $label)
  {
    $this->addFieldset($name, []);
    
    $this->fieldsets[$name]['label'] = $label;
  }
  
  public function setFieldsetDescription(string $name, string $description)
  {
    $this->addFieldset($name, []);
    
    $this->fieldsets[$name]['description'] = $description;
  }
  
  public function addFieldset($name, $fields=[])
  {
    if ( !isset($this->fieldsets[$name]) ){
      $this->fieldsets[$name] = [
        'label' => '',
        'description' => '',
        'fields' => [],
      ];
    }
    
    foreach($fields as $field){
      $this->addField($field, $name);
    }
  }
  
  public function addField($data, $fieldset='main')
  {
    $this->addFieldset($fieldset, []);
    
    foreach($this->fieldsets as $fieldsetName => $fieldsetData){
      foreach($fieldsetData['fields'] as $i => $field){
        if ( $field['name'] === $data['name'] ){
          $this->fieldsets[$fieldsetName]['fields'][$i] = array_merge($this->fieldsets[$fieldsetName]['fields'][$i], $data);
          return;
        }
      }
    }
    
    $this->fieldsets[$fieldset]['fields'][] = $data;
  }
  
  /* public function addFieldOption($fieldName, $option, $fieldset='main')
  {
    $this->addFieldset($fieldset, []);
    
    foreach($this->fieldsets as $fieldsetName => &$fieldsetData){
      foreach($fieldsetData['fields'] as $i => &$field){
        if ( $field['name'] === $fieldName ){
          if ( !$field['options'] ){
            $field['options'] = [];
          }
          $field['options'][] = $option;
          debugMe($field['options']);
          return;
        }
      }
    }
    
    return false;
  } */
  
  public function removeField($name)
  {
    foreach($this->fieldsets as $fieldsetName => $fieldset){
      foreach($fieldset['fields'] as $i => $field){
        if ( $field['name'] === $name ){
          unset($this->fieldsets[$fieldsetName]['fields'][$i]);
          return;
        }
      }
    }
  }
  
  protected function toString()
  {
    $content = [];
    
    $content[] = '<?xml version="1.0" encoding="utf-8"?>';
    $content[] = '<form>';
    
    foreach($this->fieldsets as $fieldsetName => $fieldset){
      $content[] = "\t".'<fieldset name="'.$fieldsetName.'" ';
      if ( '' !== $fieldset['label'] ){
        $content[] = "\t\t".' label="'.$this->cleanXmlAttr($fieldset['label']).'"';
      }
      if ( '' !== $fieldset['description'] ){
        $content[] = "\t\t".' description="'.$this->cleanXmlAttr($fieldset['description']).'"';
      }
      $content[] = "\t".'>';
      
      foreach($fieldset['fields'] as $field){
        $type    = null;
        $name    = null;
        $options = [];
        $attrs   = [];
        
        if ( isset($field['name']) ){
          $name = $field['name'];
          unset($field['name']);
        }
        else {
          throw new \Exception('Missing field name in ' . get_called_class());
        }
        
        $type = '';
        if ( isset($field['type']) ){
          $type = $field['type'];
          unset($field['type']);
        }
        if ( '' === $type ){
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
          $content[] = "\t\t\t".$key.'="'.$this->cleanXmlAttr($value).'"';
        }
        
        if ( $options ){
          $content[] = "\t\t".'>';
          
          foreach($options as $option){
            $content[] = "\t\t\t".'<option value="'.$this->cleanXmlAttr($option['value']).'">'.$this->cleanXmlAttr($option['text']).'</option>';
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
  
  protected function data()
  {
    return [];
  }

  protected function cleanXmlAttr($str): string
  {
    if ( 'true' === $str || true === $str ){
      $str = 'true';
    }
    
    if ( 'false' === $str || false === $str ){
      $str = 'false';
    }
    
    $str = html_entity_decode($str);
    $str = str_replace(['&','<','>','"'], ['&amp;','&lt;','&gt;','&quot;'], $str);
    $str = (string)$str;
    
    return $str;
  }
}
