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
use JDZ\Form\FormData;
use JDZ\Form\Validator\Validator;
use SimpleXMLElement;
use Exception;

/**
 * Form interface
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface FormInterface
{
  /**
   * Set the form data
   * 
   * @param  SimpleXMLElement  $xml  XML Form description
   * @return $this 
   */
  public function setXml(SimpleXMLElement $xml);
  
  /**
   * Set the form data
   * 
   * @param  FormData  $data  Form data
   * @param  bool        $reset Reset the form data
   * @return $this 
   */
  public function setData(FormData $data, $reset=false);
  
  /**
   * Set component
   * 
   * @param  string  $component 
   * @return $this 
   */
  public function setComponent($component);
  
  /**
   * Set update mode
   * 
   * @param  bool  $updateMode 
   * @return $this 
   */
  public function setUpdateMode($updateMode);
  
  /**
   * Set form orientation
   * 
   * @param  string  $orientation  One of 'vertical', 'horizontal', 'inline' (defaults to vertical)
   * @return $this 
   */
  public function setOrientation($orientation);
  
  /**
   * Set the fiel input control namespace
   * 
   * @param  string  $inputControlName
   * @return $this 
   */
  public function setInputControlName($inputControlName);
  
  /**
   * Set the form renderer
   * 
   * @param  string   $name   Renderer type
   * @return $this 
   */
  public function setRenderer($name);
  
  /**
   * Get form orientation
   * 
   * @return string 
   */
  public function getOrientation();
  
  /**
   * Get form field control namespace
   * 
   * @return string 
   */
  public function getInputControlName();
  
  /**
   * Get the form renderer object
   * 
   * @return Renderer 
   */
  public function getRenderer();
  
  /**
   * Is the form vertical
   * 
   * @return bool 
   */
  public function isVertical();
  
  /**
   * Is the form horizontal
   * 
   * @return bool 
   */
  public function isHorizontal();
  
  /**
   * Is the form inline
   * 
   * @return bool 
   */
  public function isInline();
  
  /**
   * Is the form in update mode
   * 
   * @return bool 
   */
  public function isUpdateMode();

  /**
   * Filter the form data
   * 
   * @param  array   $data   An array of field values to filter
   * @param  string  $group  The dot-separated form group path on which to filter the fields
   * @return FormData
   */
  public function filter(array $data, $group=null);
  
  /**
   * Validate form data
   * 
   * @param  FormData  $data  An Data object to validate
   * @return bool  True on success
   */
  public function validate(FormData $data);
  
  /**
   * Add an error message
   *
   * @param  Exception       $e      Exception
   * @param  FieldInterface  $field  Field name
   * @return void
   */
  public function setError(Exception $error, FieldInterface $field);
  
  /**
   * Set an attribute value for a field XML element.
   * Must be called before field is loaded
   * 
   * @param  string  $name       The name of the form field for which to set the attribute value
   * @param  string  $attribute  The name of the attribute for which to set a value
   * @param  mixed   $value      The value to set for the attribute
   * @param  string  $group      The optional dot-separated form group path on which to find the field
   * @return bool    True on success
   */
  public function setFieldAttribute($name, $attribute, $value, $group=null);
  
  /**
   * Set the value of a field. If the field does not exist in the form then the method
   * will return false.
   * Must be called before field is loaded
   * 
   * @param  string  $name   The name of the field for which to set the value
   * @param  string  $group  The optional dot-separated form group path on which to find the field
   * @param  mixed   $value  The value to set for the field
   * @return bool    True on success
   */
  public function setValue($name, $group=null, $value=null);
  
  /**
   * Return all errors, if any, as a unique string
   * 
   * @param  string   $separator   The separator
   * @return string   String containing all the errors separated by the specified sequence
   */
  public function getErrorsAsString($separator='<br />');
  
  /**
   * Return all errors, if any.
   * 
   * @return array  Array of error messages or Exception instances.
   */
  public function getErrors();
  
  /**
   * Get the form validator object
   * 
   * @return Validator
   */
  public function getValidator();
  
  /**
   * Get the form name
   * 
   * @return string  The name of the form.
   */
  public function getName();
  
  /**
   * Get the component
   * 
   * @return string  The component
   */
  public function getComponent();
  
  /**
   * Get the value of a field
   * 
   * @param  string  $name       The name of the field for which to get the value
   * @param  string  $group      The optional dot-separated form group path on which to get the value
   * @param  mixed   $default    The optional default value of the field value is empty
   * @return mixed   The value of the field or the default value if empty
   */
  public function getValue($name, $group=null, $default=null);
  
  /**
   * Get an array of fieldset objects optionally filtered over a given field group
   * 
   * @param  string  $group  The dot-separated form group path on which to filter the fieldsets
   * @return array  The array of fieldset objects
   */
  public function getFieldsets($group=null);

  /**
   * Get an array of FormField objects in a given field group by name
   * 
   * @param  string  $group   The dot-separated form group path for which to get the form fields
   * @param  bool    $nested  True to also include fields in nested groups that are inside of the
   *                            group for which to find fields
   * @return FieldInterface[]  The array of Field objects in the field group
   */
  public function getGroup($group, $nested=false);

  /**
   * Get a form field represented as a FormField object
   * 
   * @param  string  $name   The name of the form field
   * @param  string  $group  The optional dot-separated form group path on which to find the field
   * @param  mixed   $value  The optional value to use as the default for the field
   * @return mixed   The field object for the field or bool false on error
   */
  public function getField(SimpleXMLElement $element, $group=null, $value=null);
  
  /**
   * Get an attribute value from a field XML element.  If the attribute doesn't exist or is null then the optional default value will be used.
   * 
   * @param  string  $name       The name of the form field for which to get the attribute value.
   * @param  string  $attribute  The name of the attribute for which to get a value.
   * @param  mixed   $default    The optional default value to use if no attribute value exists.
   * @param  string  $group      The optional dot-separated form group path on which to find the field.
   * @return mixed  The attribute value for the field.
   */
  public function getFieldAttribute($name, $attribute, $default=null, $group=null);
  
  /**
   * Get an array of FormField objects in a given fieldset by name.  If no name is
   * given then all fields are returned
   * 
   * @param  string            $set    The optional name of the fieldset
   * @return FieldInterface[]  The array of field objects in the fieldset
   */
  public function getFieldset($set=null);
  
  /**
   * Get an error message
   *
   * @param  int  $i         Option error index
   * @param  bool  $toString  Indicates if Exception instances should return the error message or the exception object
   * @return string   Error message
   */
  public function getError($i=null, $toString=true);
  
  /**
   * Remove a field from the form definition
   * 
   * @param  string  name   The name of the form field for which remove
   * @param  string  $group  The optional dot-separated form group path on which to find the field
   * @return bool    True on success
   */
  public function removeField($name, $group=null);

  /**
   * Get an array of <field /> elements from the form XML document which are
   * in a control group by name.
   * 
   * @param  mixed   $group      The optional dot-separated form group path on which to find the fields.
   *                              Null will return all fields. False will return fields not in a group.
   * @param  bool    $nested     True to also include fields in nested groups that are inside of the
   *                              group for which to find fields.
   * @return SimpleXMLElement[]
   */
  public function &findFieldsByGroup($group=null, $nested=false);
}
