<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form;

use JDZ\Form\Exception\FormException;
use JDZ\Form\Field\Field;
use JDZ\Filesystem\File;
use JDZ\Filesystem\Path;
use SimpleXMLElement;
use RuntimeException;

/**
 * Form Helper
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class FormHelper 
{
  /**
   * Holds an array of translations
   * 
   * Defaults are in french
   * 
   * @var   array
   */
  protected static $translations = [
    'UNKNOWN_ERROR' => 'Erreur de type inconnu',
  ];
  
  /**
   * Set the translations
   *
   * @param   string  $namespace  The form namespace (fields and rules)
   * @return  void
   */
  public static function setNamespace($namespace)
  {
    Form::$ns = $namespace;
  }
  
  /**
   * Set the translations
   *
   * @param   array   $translations     Key/value pairs of translations
   * @return  void
   */
  public static function setTranslations(array $translations=[])
  {
    self::$translations = array_merge(self::$translations, $translations);
  }
  
  /**
   * Translation
   * 
   * @param   string  $key  The translation key
   * @return  string  Translated string or false if not found
   */
  public static function getTranslation($key)
  {
    $_key = strtoupper($key);
    if ( isset(self::$translations[$_key]) ){
      return self::$translations[$_key];
    }
    
    return false;
  }  
  
  /**
   * Get form XML generator instance
   * 
   * @param  string  $formName  The form file name
   * @return XmlGenerator 
   * @throws RuntimeException 
   */
  public static function loadXmlGenerator($formName)
  {
    $Class = Form::$ns.'\\'.ucfirst(Callisto()->getName()).'Bundle\\Form\\'.ucfirst($formName).'XmlGenerator';
    
    if ( !class_exists($Class) ){
      $Class = Form::$ns.'\\Form\\'.ucfirst($formName).'XmlGenerator';
      
      if ( !class_exists($Class) ){
        $Class = 'JDZ\\Form\\XmlGenerator';
        // throw new RuntimeException('Error loading form file for '.$formName);
      }
    }
    
    // debugMe($Class);
    return new $Class();
  }
  
  /**
   * Format a select/option
   * 
   * @return   object
   */
  public static function formatOption($value, $text, $disabled=false)
  {
    $option = new \stdClass;
    $option->value    = $value;
    $option->text     = trim($text);
    $option->disabled = $disabled;
    return $option;
  }  
  
  /**
   * Clean field value for readonly input
   * 
   * @return   string
   */
  public static function formatStaticValue($value)
  {
    if ( is_array($value) ){
      $value = implode('<br />', $value);
    }
    
    $value = (string)$value;
    
    // $value = strip_tags($value);
    // $value = preg_replace("/\s+/", " ", $value);
    return trim($value);
  }  
  
  /**
   * Clean field value for hidden input
   * 
   * @return   string
   */
  public static function formatHiddenValue($value)
  {
    if ( is_array($value) ){
      $value = implode(',', $value);
    }
    
    $value = (string)$value;
    
    $value = strip_tags($value);
    $value = preg_replace("/\s+/", " ", $value);
    return trim($value);
  }  
  
  /**
   * Get field label 
   * 
   * @param   string    $value  Specified value
   * @param   string    $ns     Namespace
   * @param   string    $name   Field name
   * @return   string    Defaults to $name if not found
   */
  public static function getFieldLabel($value, $ns, $name)
  {
    $value = (string) $value;
    
    if ( $value !== '' ){
      if ( $str = self::getTranslation($value) ){
        return $str;
      }
      return $value;
    }
    
    if ( $str = self::getTranslation('FIELD_'.$ns.'_'.$name.'_LABEL') ){
      return $str;
    }
    if ( $str = self::getTranslation('FIELD_'.$name.'_LABEL') ){
      return $str;
    }
    /* if ( $str = self::getTranslation($name) ){
      return $str;
    } */
    return 'FIELD_'.$ns.'_'.$name.'_LABEL';
    // return $name;
    // return '[-L-]'.$name;
  }
  
  /**
   * Get field title for table header  
   * 
   * @param   string    $value  Specified value
   * @param   string    $ns     Namespace
   * @param   string    $name   Field name
   * @return   string    Defaults to $name if not found
   */
  public static function getFieldHeader($value, $ns, $name)
  {
    $value = (string) $value;
    if ( $value !== '' ){
      if ( $str = self::getTranslation($value) ){
        return $str;
      }
      return $value;
    }
    
    if ( $str = self::getTranslation('FIELD_'.$ns.'_'.$name.'_HEADER') ){
      return $str;
    }
    
    if ( $str = self::getTranslation('FIELD_'.$name.'_HEADER') ){
      return $str;
    }
    
    // return '[-H-]'.$name;
    return self::getFieldLabel($value, $ns, $name);
  }
  
  /**
   * Get field placeholder 
   * 
   * @param   string    $value  Specified value
   * @param   string    $ns     Namespace
   * @param   string    $name   Field name
   * @return   string    Defaults to empty string if not found
   */
  /* public static function getFieldPlaceholder($value, $ns, $name)
  {
    $value = (string) $value;
    if ( $value !== '' ){
      if ( $str = self::getTranslation($value) ){
        return $str;
      }
      return $value;
    }
    if ( $str = self::getTranslation('FIELD_'.$ns.'_'.$name.'_PLACEHOLDER') ){
      return $str;
    }
    if ( $str = self::getTranslation('FIELD_'.$name.'_PLACEHOLDER') ){
      return $str;
    }
    return '';
  } */
  
  /**
   * Get field description 
   * 
   * @param   string    $value      Specified value
   * @param   string    $ns         Namespace
   * @param   string    $name       Field name
   * @param   bool      $suffix     Key suffix (DESC or DESC_UPDATE)
   * @return   string    Defaults to empty string if not found
   */
  public static function getFieldDescription($value, $ns, $name, $suffix)
  {
    $value = (string) $value;
    if ( $value !== '' ){
      if ( $str = self::getTranslation($value) ){
        return $str;
      }
      return $value;
    }
    
    if ( $str = self::getTranslation('FIELD_'.$ns.'_'.$name.'_'.$suffix) ){
      return $str;
    }
    if ( $str = self::getTranslation('FIELD_'.$name.'_'.$suffix) ){
      return $str;
    }
    
    return '';
  }
  
  /**
   * Get fieldset label 
   * 
   * @param   string    $value  Specified value
   * @param   string    $ns     Namespace
   * @param   string    $name   Field name
   * @return   string    Defaults to $name if not found
   */
  public static function getFieldsetLabel($value, $ns, $name)
  {
    $value = (string) $value;
    if ( $value !== '' ){
      if ( $str = self::getTranslation($value) ){
        return $str;
      }
      return $value;
    }
    // debugMe('FIELDSET_'.$ns.'_'.$name.'_LEGEND');
    if ( $str = self::getTranslation('FIELDSET_'.$ns.'_'.$name.'_LEGEND') ){
      return $str;
    }
    if ( $str = self::getTranslation('FIELDSET_'.$name.'_LEGEND') ){
      return $str;
    }
    // if ( $str = self::getTranslation($name) ){
      // return $str;
    // }
    // return 'FIELDSET_'.$ns.'_'.$name.'_LEGEND';
    // return '[-F-]'.$name;
    return ''; //'FIELDSET_'.$ns.'_'.$name.'_LEGEND';
  }
  
  /**
   * Get fieldset description 
   * 
   * @param   string    $value  Specified value
   * @param   string    $ns     Namespace
   * @param   string    $name   Field name
   * @return   string    Defaults to empty string if not found
   */
  public static function getFieldsetDescription($value, $ns, $name)
  {
    $value = (string) $value;
    if ( $value !== '' ){
      if ( $str = self::getTranslation($value) ){
        return $str;
      }
      return $value;
    }
    if ( $str = self::getTranslation('FIELDSET_'.$ns.'_'.$name.'_DESC') ){
      return $str;
    }
    if ( $str = self::getTranslation('FIELDSET_'.$name.'_DESC') ){
      return $str;
    }
    
    return '';
  }
  
  /**
   * Get required error message
   * 
   * @param   string    $value  Specified value
   * @param   string    $ns     Namespace
   * @param   string    $name   Field name
   * @return   string    (defaults to ERROR_FORM_FIELD_REQUIRED if not found)
   */
  public static function getRequiredError($value, $ns, $name)
  {
    $value = (string) $value;
    if ( $value !== '' ){
      if ( $str = self::getTranslation($value) ){
        return $str;
      }
      return $value;
    }
    if ( $str = self::getTranslation('ERROR_FORM_FIELD_'.$ns.'_'.$name.'_REQUIRED') ){
      return $str;
    }
    if ( $str = self::getTranslation('ERROR_FORM_FIELD_'.$name.'_REQUIRED') ){
      return $str;
    }
    
    $fieldLabel = self::getFieldLabel('', $ns, $name);
    return self::getTranslation('ERROR_FORM_FIELD_REQUIRED').' : '.$fieldLabel;
  }
  
  /**
   * Get rule error message
   * 
   * @param   string    $value  Specified value
   * @param   string    $ns     Namespace
   * @param   string    $name   Field name
   * @return   string    (defaults to ERROR_FORM_FIELD_VALIDATE if not found)
   */
  public static function getRuleError($value, $ns, $name, $rule)
  {
    $value = (string) $value;
    if ( $str = self::getTranslation('ERROR_FORM_FIELD_'.$ns.'_'.$name.'_'.$rule.'_VALIDATE') ){
      return $str;
    }
    if ( $str = self::getTranslation('ERROR_FORM_FIELD_'.$name.'_'.$rule.'_VALIDATE') ){
      return $str;
    }
    if ( $str = self::getTranslation('ERROR_FORM_FIELD_'.$ns.'_'.$name.'_VALIDATE') ){
      return $str;
    }
    if ( $str = self::getTranslation('ERROR_FORM_FIELD_'.$name.'_VALIDATE') ){
      return $str;
    }
    $fieldLabel = self::getFieldLabel('', $ns, $name);
    return self::getTranslation('ERROR_FORM_FIELD_VALIDATE').' : '.$fieldLabel;
  }
}