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
use RuntimeException;

/**
 * Abstract Rule
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class Rule implements RuleInterface
{
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
   * Rules namepace
   * 
   * @var    string   
   */
  protected static $NS;
  
  /**
   * Instances
   * 
   * @var    array   
   */
  protected static $instances;
  
  /**
   * Set the rules namespace
   * 
   * @param   string  $NS  The rules namespace
   * @return  void
   */
  public static function setNamespace($NS)
  {
    self::$NS = $NS;
  }
  
  /**
   * Get a field instance
   * 
   * @param   string  $name  The field type
   * @return   Rule    Rule instance clone
   * @throws   RuntimeException
   */
  public static function getInstance($type)
  {
    if ( !isset(self::$NS) ){
      self::$NS = '\\Form\\Rule\\';
    }
    
    if ( !isset(self::$instances) ){
      self::$instances = [];
    }
    
    if ( empty($type) ){
      throw new RuntimeException('Missing rule type');
    }
    
    if ( !isset(self::$instances[$type]) ){
      $Class = self::$NS.ucfirst($type);
      
      if ( !class_exists($Class) ){
        throw new RuntimeException('Unrecognized rule type :: '.$type);
      }
      
      self::$instances[$type] = new $Class();
    }
    
    return clone self::$instances[$type];
  }  
  
  /**
   * {@inheritDoc}
   */
  public function test(SimpleXMLElement &$element, $value, $group=null, Registry &$input=null, Form &$form=null)
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
    
    return preg_match('/'.$this->regex.'/'.$this->modifiers, $value, $m);
  }
}
