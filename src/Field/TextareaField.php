<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Field;

use JDZ\Form\FormHelper;
use RuntimeException;

/**
 * Abstract Textarea field
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class TextareaField extends Field
{
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
   * Field cols attribute
   * 
   * @var    int   
   */
  protected $cols;
  
  /**
   * Field rows attribute
   * 
   * @var    int   
   */
  protected $rows;
  
  /**
   * Field placeholder attribute
   * 
   * @var    string   
   */
  protected $placeholder;
  
  public function getFieldAttributes(array $attrs=[]): array
  {
    $attrs = parent::getFieldAttributes($attrs);
    
    if ( $this->size > 0 ){
      $attrs['size'] = $this->size;
    }
    
    if ( $this->maxlength > 0 ){
      $attrs['maxlength'] = $this->maxlength;
    }
    
    if ( $this->cols > 0 ){
      $attrs['cols'] = $this->cols;
    }
    
    if ( $this->rows > 0 ){
      $attrs['rows'] = $this->rows;
    }
    
    if ( $this->placeholder !== '' ){
      $attrs['placeholder'] = $this->placeholder;
    }
    
    return $attrs;
  }
  
  public function getStaticValue(): string
  {
    $value='NEED TO IMPLEMENT READONLY FOR TEXTAREA';
    // $value = nl2br($this->value);
    return FormHelper::formatStaticValue($value);
  }
  
  public function getHiddenValue(): string
  {
    throw new RuntimeException('Textarea cannot be formatted as hidden !'); 
  }
  
  protected function initDefinition()
  {
    parent::initDefinition();
    
    $this->defAttribute('filter', '\JDZ\Helpers\StringHelper::cleanTextarea');
    $this->defAttribute('size', '0');
    $this->defAttribute('maxlength', '0');
    $this->defAttribute('cols', '0');
    $this->defAttribute('rows', '0');
    
    $this->setAttribute('spanBefore', '');
    $this->setAttribute('spanAfter', '');
    $this->setAttribute('inputgroupClass', '');
  }
  
  protected function initObject()
  {
    parent::initObject();

    $this->size        = (int) $this->element['size'];
    $this->maxlength   = (int) $this->element['maxlength'];
    $this->cols        = (int) $this->element['cols'];
    $this->rows        = (int) $this->element['rows'];
    $this->placeholder = (string) $this->element['placeholder'];
    $this->filter      = (string) $this->element['filter'];
  }
  
  protected function renderField(array $attrs=[]): array
  {
    return array_merge(parent::renderField($attrs), [
      'type' => 'textarea',
      'attrs' => $this->getFieldAttributes($attrs),
      'content' => $this->value,
    ]);
  }
}
