<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Field;

use JDZ\Form\Form;
use JDZ\Form\FormHelper;
use JDZ\Registry\Registry;
use JDZ\Helpers\AttributesHelper;

/**
 * Abstract Input field
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class InputField extends Field
{
  /**
   * Field type attribute
   * 
   * @var    string   
   */
  protected $type;
  
  /**
   * Field size attribute
   * 
   * @var    int   
   */
  protected $size;
  
  /**
   * Field maxlength attribute
   * 
   * @var    int   
   */
  protected $maxlength;
  
  /**
   * Field placeholder attribute
   * 
   * @var    string   
   */
  protected $placeholder;
  
  /**
   * Field pattern attribute
   * 
   * @var    string   
   */
  protected $pattern;
  
  /**
   * Check if field is a hidden input 
   * 
   * @return   bool
   */
  public function isHidden()
  {
    return ( $this->hidden );
    // return ( $this->type === 'hidden' );
  }
  
  /**
   * {@inheritDoc}
   */
  public function getFieldAttributes(array $attrs=[])
  {
    $attrs = parent::getFieldAttributes($attrs);
    
    $attrs['type'] = $this->type;
    
    if ( $this->size > 0 ){
      $attrs['size'] = $this->size;
    }
    
    if ( $this->maxlength > 0 ){
      $attrs['maxlength'] = $this->maxlength;
    }
    
    if ( $this->pattern !== '' ){
      $attrs['pattern'] = $this->pattern;
    }
    
    if ( $this->placeholder !== '' ){
      $attrs['placeholder'] = $this->placeholder;
    }
    
    $attrs['value'] = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
    
    return $attrs;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getStaticValue()
  {
    $this->bsInputgroupPrefix = trim($this->bsInputgroupPrefix);
    
    $value = FormHelper::formatStaticValue($this->value);
    
    if ( $this->bsInputgroupPrefix === '' ){
      return $value;
    }
    
    if ( $this->bsInputgroupSuffix === '' ){
      return $value;
    }
    
    return '['.$this->bsInputgroupPrefix.'] '.$value;
  }
  
  /**
   * {@inheritDoc}
   */
  protected function initDefinition()
  {
    parent::initDefinition();
    
    $this->setAttribute('type', 'text');
    $this->setAttribute('canBeStatic', 'true');
    
    $this->defAttribute('size', '0', 'int');
    $this->defAttribute('maxlength', '0', 'int');
    $this->defAttribute('placeholder', '');
    $this->defAttribute('pattern', '');
  }
  
  /**
   * {@inheritDoc}
   */
  protected function initObject()
  {
    parent::initObject();

    $this->type         = (string) $this->element['type'];
    $this->size         = (int) $this->element['size'];
    $this->maxlength    = (int) $this->element['maxlength'];
    $this->placeholder  = (string) $this->element['placeholder'];
    $this->pattern      = (string) $this->element['pattern'];
  }
  
  /**
   * {@inheritDoc}
   */
  protected function renderField(array $attrs=[])
  {
    return [
      'type' => 'input',
      'attrs' => $this->getFieldAttributes($attrs),
    ];
  }
}
