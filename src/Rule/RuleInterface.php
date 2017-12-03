<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Rule;

use JDZ\Form\Form;
use JDZ\Registry\Registry;
use SimpleXMLElement;

/**
 * Rule base class
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface RuleInterface
{
  /**
   * Run the test
   * 
   * @param   SimpleXMLElement    &$element  The SimpleXMLElement object representing the <field /> tag for the form field object
   * @param   mixed               $value     The form field value to validate
   * @param   string              $group     The field name group control value. This acts as as an array container for the field.
   *                                         For example if the field has name="foo" and the group value is set to "bar" then the
   *                                         full field name would end up being "bar[foo]".
   * @param   Registry            &$input    An optional Registry object with the entire data set to validate against the entire form
   * @param   Form                &$form     The form object for which the field is being tested
   * @return   boolean  True if the value is valid
   */
  public function test(SimpleXMLElement &$element, $value, $group=null, Registry &$input=null, Form &$form=null);
}
