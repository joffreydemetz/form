<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Field;

/**
 * Abstract Select field
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class SelectField extends Field
{
  /**
   * Field size attribute
   * 
   * @var    int   
   */
  protected $size;
  
  /**
   * Field multiple attribute
   * 
   * @var    bool   
   */
  protected $multiple;
  
  /**
   * Add a default option
   * 
   * @var    bool   
   */
  protected $defaultOption;
  
  /**
   * Current selection
   * 
   * @var    array   
   */
  protected $selection;
  
  /**
   * {@inheritcdoc}
   */
  public function getFieldAttributes(array $attrs=[])
  {
    $attrs = parent::getFieldAttributes($attrs);
    
    if ( $this->size > 0 ){
      $attrs['size'] = $this->size;
    }
    
    if ( $this->multiple === true ){
      $attrs['multiple'] = 'multiple';
    }
    
    return $attrs;
  }
  
  /**
   * {@inheritDoc}
   */
  public function isEmpty()
  {
    return ( count($this->selection) === 0 );
  }
  
  /**
   * Check if a value in in the selection dataset
   * 
   * @return   bool
   */
  public function isSelected($testValue)
  {
    return ( in_array($testValue, $this->selection) );
  }
  
  /**
   * {@inheritcdoc}
   */
  protected function initDefinition()
  {
    parent::initDefinition();
    
    $this->defAttribute('size', '0', 'int');
    $this->defAttribute('multiple', 'false', 'bool');
    $this->defAttribute('defaultOption', 'true', 'bool');
  }
  
  /**
   * {@inheritcdoc}
   */
  protected function initObject()
  {
    parent::initObject();

    $this->size           = (int) $this->element['size'];
    $this->multiple       = ( (string) $this->element['multiple'] === 'true' );
    $this->defaultOption  = ( (string) $this->element['defaultOption'] === 'true' );
  }
  
  /**
   * {@inheritcdoc}
   */
  protected function checkValue()
  {
    if ( is_array($this->value) ){
      $this->value = implode(',', $this->value);
    }
    
    $this->value     = (string)$this->value;
    $this->selection = [];
    
    if ( $this->multiple === true ){
      if ( $this->value !== '' ){
        $this->selection = explode(',', $this->value);
      }
    }
    elseif ( $this->value !== '' ) {
      $this->selection[] = $this->value;
    }
  }
}
