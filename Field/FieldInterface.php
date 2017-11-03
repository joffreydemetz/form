<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Field;

use JDZ\Form\Form;
use RuntimeException;
use SimpleXMLElement;

/**
 * Field Base Class
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface FieldInterface
{
	/**
	 * Init field
   * 
	 * @param 	Form                  $form     Form instance.
   * @param 	SimpleXMLElement      $value    SimpleXMLElement object representing the <field /> tag for the form field object
   * @param 	mixed                 $group    Field group. This acts as an array container for the field
	 *                                          For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                          full field name would end up being "bar[foo]"
   * @param 	mixed                 $value    Field value
	 * @return 	void
	 */
	public function init(Form &$form, SimpleXMLElement &$element, $group=null, $value=null);
  
	/**
	 * Set field attribute in XML definition
   * 
   * @param 	string    $attribute    The attribute name
   * @param 	string    $value        The attribute value
   * @param 	string    $type         The value type
   * @return 	void
	 */
  public function setAttribute($attribute, $value='', $type='string');
  
	/**
	 * Define field attribute in XML definition
   * 
   * @param 	string    $attribute    The attribute name
   * @param 	string    $default      The attribute default value
   * @param 	string    $type         The value type
   * @return 	void
	 */
  public function defAttribute($attribute, $default=null, $type='string');
  
	/**
	 * Clean the field Object before rendering it
   * 
   * Checks if a readonly field must be hidden or static
   *
   * @return 	void
	 */
	public function cleanForRender();
  
  /**
	 * Get the field input markup.
   * 
	 * @param 	string  $indent   HTML indent
	 * @param 	array   $attrs    Key/Value pairs of field attributes (optionnal)
	 * @return 	string  HTML.
	 */
	public function getFieldHtml(array $attrs=[]);
	
	/**
	 * Get the field value when readonly or disabled
   * 
	 * @return 	string  The field readonly value.
	 */
  public function getStaticValue();
  
	/**
	 * Get the field value when hidden
   * 
	 * @return 	string  The field hidden value.
	 */
  public function getHiddenValue();
  
	/**
	 * Check if a value was set for the field
   * 
	 * @return 	bool  True if the value was set.
	 */
  public function isEmpty();
  
	/**
	 * Check if field is a hidden input 
   * 
   * @return 	bool
	 */
	public function isHidden();
  
	/**
	 * Get field attributes
   * 
	 * @return 	array   Key/Value pairs of html field attributes
	 */
  public function getFieldAttributes(array $attrs=[]);
	
	/**
	 * Get the field container class
   * 
	 * @return 	array  The container classes.
	 */
  public function getContainerClass();
  
	/**
	 * Get the field label class
   * 
	 * @return 	array  The label classes.
	 */
  public function getLabelClass();
  
	/**
	 * Get the field class
   * 
	 * @return 	array  The field classes.
	 */
  public function getFieldClass();
}
