<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form;

use JDZ\Form\Field\FieldInterface;
use JDZ\Form\Renderer\Renderer;
use JDZ\Form\Validator\Validator;
use SimpleXMLElement;

/**
 * Form interface
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface FormInterface
{
  /**
   * Get an option value
   * 
   * @param   string  $property   Option property
   * @return   mixed   The property value or false if not set
   */
  public function getFormOption($property);
  
  /**
   * Get a config option 
   * 
   * @param   string  $key  The config option key
   * @return   string  The form config option value
   */
  public function getOption($key, $default='');
  
  /**
   * Load the form description from an SimpleXMLElement object
   * 
   * The replace option works per field. If a field being loaded already exists in the current
   * form definition then the behavior of load will vary depending upon the replace flag. If it
   * is set to true, then the existing field will be replaced in its exact location by the new
   * field being loaded. If it is false, then the new field being loaded will be ignored and the
   * method will move on to the next field to load.
   * 
   * @param   SimpleXMLElement  $xml     The name of an XML object
   * @param   string            $replace  Flag to toggle whether form fields should be replaced if a field already exists with the same group/name
   * @param   string|false      $xpath    An optional xpath to search for the fields
   * @return   void
   */
  public function load(SimpleXMLElement $xml, $replace=true, $xpath=false);
  
  /**
   * Bind data to the form
   * 
   * @param   mixed  $data  An array or object of data to bind to the form
   * @return   bool  True on success
   */
  public function bind($data);
  
  /**
   * Reset the form data store and optionally the form XML definition
   * 
   * @param   bool  $xml  True to also reset the XML form definition
   * @return   bool  True on success
   */
  public function reset($xml=false);

  /**
   * Filter the form data
   * 
   * @param   array   $data   An array of field values to filter
   * @param   string  $group  The dot-separated form group path on which to filter the fields
   * @return   array|bool
   */
  public function filter($data, $group=null);

  /**
   * Validate form data.
   * Validation warnings will be pushed with Form::setError and should be
   * retrieved with Form::getErrors() when validate returns boolean false
   * 
   * @param   array   $data   An array of field values to validate.
   * @param   string  $group  The optional dot-separated form group path on which to filter the
   *                          fields to be validated
   * @return   mixed   True on sucess
   */
  public function validate($data, $group=null);
  
  /**
   * Add an error message.
   *
   * @param   string|Exception  $error  Error message
   * @return   void
   */
  public function setError($error);
  
  /**
   * Set an attribute value for a field XML element.
   * Must be called before field is loaded
   * 
   * @param   string  $name       The name of the form field for which to set the attribute value
   * @param   string  $attribute  The name of the attribute for which to set a value
   * @param   mixed   $value      The value to set for the attribute
   * @param   string  $group      The optional dot-separated form group path on which to find the field
   * @return   bool    True on success
   */
  public function setFieldAttribute($name, $attribute, $value, $group=null);
  
  /**
   * Set the value of a field. If the field does not exist in the form then the method
   * will return false.
   * Must be called before field is loaded
   * 
   * @param   string  $name   The name of the field for which to set the value
   * @param   string  $group  The optional dot-separated form group path on which to find the field
   * @param   mixed   $value  The value to set for the field
   * @return   bool    True on success
   */
  public function setValue($name, $group=null, $value=null);
  
  /**
   * Set some field XML elements to the form definition.  If the replace flag is set then
   * the fields will be set whether they already exists or not.  If it isn't set, then the fields
   * will not be replaced if they already exist.
   * Must be called before field is loaded
   * 
   * @param   object  &$elements  The array of XML element object representations of the form fields
   * @param   string  $group      The optional dot-separated form group path on which to set the fields
   * @param   bool    $replace    True to replace existing fields if they already exist
   * @return   bool    True on success
   */
  public function setFields(&$elements, $group=null, $replace=true);
  
  /**
   * Set a field XML element to the form definition.  If the replace flag is set then
   * the field will be set whether it already exists or not.  If it isn't set, then the field
   * will not be replaced if it already exists
   * Must be called before field is loaded
   * 
   * @param   object  &$element  The XML element object representation of the form field
   * @param   string  $group     The optional dot-separated form group path on which to set the field
   * @param   bool    $replace   True to replace an existing field if one already exists
   * @return   bool    True on success
   */
  public function setField(&$element, $group=null, $replace=true);
  
  /**
   * Get the form validator object
   * 
   * @return   Validator
   */
  public function getValidator();
  
  /**
   * Get the form renderer object
   * 
   * @return   Renderer 
   */
  public function getRenderer();
  
  /**
   * Get the form name
   * 
   * @return   string  The name of the form.
   */
  public function getName();
  
  /**
   * Get the form context
   * 
   * @return   string  The context
   */
  public function getContext();
  
  /**
   * Get the value of a field
   * 
   * @param   string  $name       The name of the field for which to get the value
   * @param   string  $group      The optional dot-separated form group path on which to get the value
   * @param   mixed   $default    The optional default value of the field value is empty
   * @return   mixed   The value of the field or the default value if empty
   */
  public function getValue($name, $group=null, $default=null);
  
  /**
   * Get an array of fieldset objects optionally filtered over a given field group
   * 
   * @param   string  $group  The dot-separated form group path on which to filter the fieldsets
   * @return   array  The array of fieldset objects
   */
  public function getFieldsets($group=null);

  /**
   * Get an array of FormField objects in a given field group by name
   * 
   * @param   string  $group   The dot-separated form group path for which to get the form fields
   * @param   bool    $nested  True to also include fields in nested groups that are inside of the
   *                            group for which to find fields
   * @return   FieldInterface[]  The array of Field objects in the field group
   */
  public function getGroup($group, $nested=false);

  /**
   * Get a form field represented as a FormField object
   * 
   * @param   string  $name   The name of the form field
   * @param   string  $group  The optional dot-separated form group path on which to find the field
   * @param   mixed   $value  The optional value to use as the default for the field
   * @return   mixed   The field object for the field or boolean false on error
   */
  public function getField($name, $group=null, $value=null);
  
  /**
   * Get an attribute value from a field XML element.  If the attribute doesn't exist or is null then the optional default value will be used.
   * 
   * @param   string  $name       The name of the form field for which to get the attribute value.
   * @param   string  $attribute  The name of the attribute for which to get a value.
   * @param   mixed   $default    The optional default value to use if no attribute value exists.
   * @param   string  $group      The optional dot-separated form group path on which to find the field.
   * @return   mixed  The attribute value for the field.
   */
  public function getFieldAttribute($name, $attribute, $default=null, $group=null);
  
  /**
   * Remove a field from the form definition
   * 
   * @param   string  name   The name of the form field for which remove
   * @param   string  $group  The optional dot-separated form group path on which to find the field
   * @return   bool    True on success
   */
  public function removeField($name, $group=null);

  /**
   * Remove a group from the form definition
   * 
   * @param   string  $group  The dot-separated form group path for the group to remove
   * @return   bool  True on success
   */
  public function removeGroup($group);

  /**
   * Get an array of FormField objects in a given fieldset by name.  If no name is
   * given then all fields are returned
   * 
   * @param   string            $set    The optional name of the fieldset
   * @return   FieldInterface[]  The array of field objects in the fieldset
   */
  public function getFieldset($set=null);
  
  /**
   * Get an array of <field /> elements from the form XML document which are
   * in a control group by name.
   * 
   * @param   mixed   $group      The optional dot-separated form group path on which to find the fields.
   *                              Null will return all fields. False will return fields not in a group.
   * @param   bool    $nested     True to also include fields in nested groups that are inside of the
   *                              group for which to find fields.
   * @return   SimpleXMLElement[]
   */
  public function &findFieldsByGroup($group=null, $nested=false);
}
