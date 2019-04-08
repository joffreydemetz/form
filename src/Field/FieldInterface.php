<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Field;

use JDZ\Form\Form;
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
   * @param   mixed  $value  Field value
   * @return  void
   */
  // public function init($value=null);
  
  /**
   * Set the object properties based on a named array/hash.
   *
   * @param   mixed  $properties  Either an associative array or another object.
   * @return   boolean
   */
  // public function setProperties($properties);

  /**
   * Modifies a property of the object, creating it if it does not already exist.
   *
   * @param   string  $property  The name of the property.
   * @param   mixed   $value     The value of the property to set.
   * @return   mixed  Previous value of the property.
   */
  // public function set($property, $value = null);

  /**
   * Clears a property.
   *
   * @param   string  $property  The name of the property.
   * @return   void
   */
  // public function erase($property);
  
  /**
   * Set the form
   * 
   * @param  Form  $form  Form instance
   * @return void
   */
  // public function setForm(Form $form);
  
  /**
   * Set the XML element
   * 
   * @param  SimpleXMLElement  $element  SimpleXMLElement instance
   * @return void
   */
  // public function setElement(SimpleXMLElement $element);
  
  /**
   * Set the group
   * 
   * @param  string  $group  
   * @return void
   */
  // public function setGroup($group);
  
  /**
   * Set the value
   * 
   * @param   mixed    $value   The field value
   * @return   void
   */
  // public function setValue($value=null);
  
  /**
   * Set field attribute in XML definition
   * 
   * @param   string    $attribute    The attribute name
   * @param   string    $value        The attribute value
   * @param   string    $type         The value type
   * @return   void
   */
  // public function setAttribute($attribute, $value='', $type='string');
  
  /**
   * Define field attribute in XML definition
   * 
   * @param   string    $attribute    The attribute name
   * @param   string    $default      The attribute default value
   * @param   string    $type         The value type
   * @return   void
   */
  // public function defAttribute($attribute, $default=null, $type='string');
  
  /**
   * Returns a property of the object or the default value if the property is not set.
   *
   * @param   string  $property  The name of the property.
   * @param   mixed   $default   The default value.
   * @return   mixed    The value of the property.
   */
  // public function get($property, $default = null);
  
  /**
   * Test if the property exists
   *
   * @param  string  $property  The name of the property.
   * @return bool
   */
  // public function has($property);
  
  /**
   * Returns an associative array of object properties.
   *
   * @param   boolean  $public  If true, returns only the public properties.
   * @return   array
   */
  // public function export();
  
  /**
   * Get the field input markup.
   * 
   * @param   string  $indent   HTML indent
   * @param   array   $attrs    Key/Value pairs of field attributes (optionnal)
   * @return   string  HTML.
   */
  // public function getFieldHtml(array $attrs=[]);
  
  /**
   * Get the field value when readonly or disabled
   * 
   * @return   string  The field readonly value.
   */
  // public function getStaticValue();
  
  /**
   * Get the field value when hidden
   * 
   * @return   string  The field hidden value.
   */
  // public function getHiddenValue();
  
  /**
   * Get field attributes
   * 
   * @return   array   Key/Value pairs of html field attributes
   */
  // public function getFieldAttributes(array $attrs=[]);
  
  /**
   * Get the field container class
   * 
   * @return   array  The container classes.
   */
  // public function getContainerClasses();
  
  /**
   * Get the field label class
   * 
   * @return   array  The label classes.
   */
  // public function getLabelClasses();
  
  /**
   * Get the field class
   * 
   * @return   array  The field classes.
   */
  // public function getFieldClasses();
  
  /**
   * Check if a value was set for the field
   * 
   * @return   bool  True if the value was set.
   */
  // public function isEmpty();
  
  /**
   * Check if field is a hidden input 
   * 
   * @return   bool
   */
  // public function isHidden();
  
  /**
   * Clean the field Object before rendering it
   * 
   * Checks if a readonly field must be hidden or static
   *
   * @return   void
   */
  // public function cleanForRender();
}
