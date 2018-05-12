<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Rule;

use JDZ\Form\Form;
use JDZ\Form\FormHelper;
use JDZ\Form\FormData;
use SimpleXMLElement;
use RuntimeException;

/**
 * Abstract Rule
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class Rule implements RuleInterface
{
  /**
   * Form 
   * 
   * @var    Form   
   */
  protected $form;
  
  /**
   * Field XML definition
   * 
   * @var    SimpleXMLElement   
   */
  protected $element;
  
  /**
   * Field group
   * 
   * @var    string   
   */
  protected $group;
  
  /**
   * Form Data
   * 
   * @var    FormData   
   */
  protected $data;
  
  /**
   * The regular expression to use in testing a form field value
   * 
   * @var    string   
   */
  protected $regex;
  
  /**
   * The regular expression modifiers to use when testing a form field value
   * 
   * @var    string   
   */
  protected $modifiers;
  
  /**
   * {@inheritDoc}
   */
  public function setForm(Form $form)
  {
    $this->form = $form;
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setElement(SimpleXMLElement $element)
  {
    $this->element = $element;
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setGroup($group=null)
  {
    $this->group = $group;
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function setData(FormData $data)
  {
    $this->data = $data;
    return $this;
  }
  
  /**
   * {@inheritDoc}
   */
  public function test($value)
  {
    if ( empty($this->regex) ){
      throw new RuntimeException('Invalid rule ['.get_class($this).']');
    }
    
    /**
     * UNICODE
     * utf8/unicode compat
     * @see http://php.net/manual/fr/reference.pcre.pattern.modifiers.php
     * @see https://hsivonen.fi/php-utf8/
     * Detect if we have full UTF-8 and unicode PCRE support.
     */
    if ( @preg_match('/\pL/u', 'a') ){
      if ( strpos($this->modifiers, 'u') === false ){
        $this->modifiers .= 'u';
      }
    }
    
    return ( preg_match('/'.$this->regex.'/'.$this->modifiers, $value) );
  }
}
