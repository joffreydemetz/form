<?php 
/**
 * THIS SOFTWARE IS PRIVATE
 * CONTACT US FOR MORE INFORMATION
 * Joffrey Demetz <joffrey.demetz@gmail.com>
 * <https://callisto-framework.com>
 */
namespace JDZ\Form;

/**
 * Data Object
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class FormData
{
  /**
   * Constructor 
   * 
   * @param  array|object|null  $properties  Key/Value pairs
   */
  public function __construct($properties=null)
  {
    if ( null !== $properties ){
      $this->setProperties($properties);
    }
  }
  
  /**
   * Returns the record data as an associative array or object
   *
   * @param  bool  $object  True to return a stdClass
   * @return array|stdClass
   */
  public function all($object=false)
  {
    $properties = get_object_vars($this);
    
    if ( $object ){
      return (object)$properties;
    }
    
    return $properties;
  }  
  
  /**
   * Returns an associative array of object properties
   *
   * @param   bool  $object  True to return a stdClass
   * @return  array|\stdClass
   */
  public function getProperties($object=true)
  {
    return $this->all($object);
  }
  
  /**
   * Return an associative array of object properties
   *
   * @return  array
   */
  public function export()
  {
    return $this->all(false);
  }
  
  /**
   * Returns a property of the object or the default value if the property is not set
   * 
   * @param   string  $key  The name of the property
   * @param   mixed   $default   The default value
   * @return  mixed   The value of the property
   */
  public function get($key, $default=null)
  {
    if ( isset($this->{$key}) ){
      return $this->{$key};
    }
    return $default;
  }
  
  /**
   * Sets the default value of a property
   * 
   * @param   string  $key    The name of the property
   * @param   mixed   $value  The default value
   * @return  mixed   The value of the property
   */
  public function def($key, $value)
  {
    if ( !isset($this->{$key}) ){
      $this->set($key, $value);
    }
    return $this;
  }
  
  /**
   * Set the object properties
   * 
   * @param   mixed  $properties  Either an associative array or another object
   * @return  void
   */
  public function setProperties($properties)
  {
    if ( is_array($properties) || is_object($properties) ){
      foreach((array)$properties as $k => $v){
        $this->set($k, $v);
      }
    }
    return $this;
  }
  
  /**
   * Modifies a property of the object, creating it if it does not already exist
   *
   * @param   string  $key    The name of the property
   * @param   mixed   $value  The value of the property to set
   * @return  void
   */
  public function set($key, $value=null)
  {
    $this->{$key} = $value;
    return $this;
  }
  
  /**
   * Is the property set in the object
   * 
   * @param   string  $key  The name of the property
   * @return  bool    True if the property exists
   */
  public function has($key)
  {
    return property_exists($this, $key);
  }
  
  /**
   * Clears a property
   *
   * @param   string  $key  The name of the property
   * @return  void
   */
  public function erase($key)
  {
    if ( $this->has($key) ){
      unset($this->{$key});
    }
    return $this;
  }
}
