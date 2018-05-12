<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Rule;

use JDZ\Form\Form;
use JDZ\Form\FormData;
use SimpleXMLElement;

/**
 * Rule base class
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface RuleInterface
{
  /**
   * Set the form
   * 
   * @param  Form  $form  Form instance
   * @return $this
   */
  public function setForm(Form $form);
  
  /**
   * Set the XML element
   * 
   * @param  SimpleXMLElement  $element  SimpleXMLElement instance
   * @return $this
   */
  public function setElement(SimpleXMLElement $element);
  
  /**
   * Set the group
   * 
   * @param  string  $group  
   * @return $this
   */
  public function setGroup($group);
  
  /**
   * Set the XML element
   * 
   * @param  FormData  $data  Form data
   * @return $this
   */
  public function setData(FormData $data);
  
  /**
   * Run the test
   * 
   * @param   mixed  $value  The form field value to validate
   * @return  bool   True if the value is valid
   * @throws  RuntimeException
   */
  public function test($value);
}
