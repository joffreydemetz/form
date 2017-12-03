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
  
  /**
   * {@inheritcdoc}
   */
  public function getFieldAttributes(array $attrs=[])
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
  
  /**
   * {@inheritDoc}
   */
  public function getStaticValue()
  {
    $value='NEED TO IMPLEMENT READONLY FOR TEXTAREA';
    // $value = nl2br($this->value);
    return FormHelper::formatStaticValue($value);
  }
  
  /**
   * {@inheritDoc}
   */
  public function getHiddenValue()
  {
    throw new RuntimeException('Textarea cannot be formatted as hidden !'); 
  }
  
  /**
   * {@inheritcdoc}
   */
  protected function initDefinition()
  {
    parent::initDefinition();
    
    // $this->setAttribute('filter', 'html');
    $this->setAttribute('filter', '\JDZ\Helpers\StringHelper::cleanTextarea');
    
    $this->defAttribute('size', '0', 'int');
    $this->defAttribute('maxlength', '0', 'int');
    $this->defAttribute('cols', '0', 'int');
    $this->defAttribute('rows', '0', 'int');
    $this->defAttribute('placeholder', '');
  }
  
  /**
   * {@inheritcdoc}
   */
  protected function initObject()
  {
    parent::initObject();

    $this->size        = (int) $this->element['size'];
    $this->maxlength   = (int) $this->element['maxlength'];
    $this->cols        = (int) $this->element['cols'];
    $this->rows        = (int) $this->element['rows'];
    $this->placeholder = (string) $this->element['placeholder'];
  }
  
  /**
   * {@inheritcdoc}
   */
  protected function renderField(array $attrs=[])
  {
    return [
      'type' => 'textarea',
      'attrs' => $this->getFieldAttributes($attrs),
      'content' => $this->value,
    ];
  }
}
