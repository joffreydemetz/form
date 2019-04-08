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
  
  public function getType(): string
  {
    return $this->type;
  }
  
  public function getPlaceholder(): string
  {
    return $this->placeholder;
  }
  
  public function getPattern(): string
  {
    return $this->pattern;
  }
  
  public function getSize(): int
  {
    return $this->size;
  }
  
  public function getMaxlength(): int
  {
    return $this->maxlength;
  }
  
  /**
   * Check if field is a hidden input 
   * 
   * @return   bool
   */
  /* public function isHidden(): bool
  {
    return 'hidden' === $this->type;
  } */
  
  public function getFieldAttributes(array $attrs=[]): array
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
  
  public function getStaticValue(): string
  {
    $this->spanBefore = trim($this->spanBefore);
    
    $value = FormHelper::formatStaticValue($this->value);
    
    if ( $this->spanBefore === '' ){
      return $value;
    }
    
    if ( $this->spanAfter === '' ){
      return $value;
    }
    
    return '['.$this->spanBefore.'] '.$value;
  }
  
  protected function initDefinition()
  {
    parent::initDefinition();
    
    $this->setAttribute('type', 'text');
    $this->setAttribute('canBeStatic', 'true');
    
    $this->defAttribute('size', '0');
    $this->defAttribute('maxlength', '0');
    $this->defAttribute('placeholder', '');
    $this->defAttribute('pattern', '');
  }
  
  protected function initObject()
  {
    parent::initObject();

    $this->type         = (string) $this->element['type'];
    $this->size         = (int) $this->element['size'];
    $this->maxlength    = (int) $this->element['maxlength'];
    $this->placeholder  = (string) $this->element['placeholder'];
    $this->pattern      = (string) $this->element['pattern'];
  }
  
  protected function renderField(array $attrs=[]): array
  {
    return array_merge(parent::renderField($attrs), [
      'type' => 'input',
      'attrs' => $this->getFieldAttributes($attrs),
    ]);
  }
}
