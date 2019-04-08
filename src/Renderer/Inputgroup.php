<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Renderer;

use JDZ\Form\Form;
use JDZ\Form\FormHelper;
use JDZ\Form\Field\Button;
use JDZ\Form\Field\Field;
use JDZ\Form\Field\FieldInterface;
use SimpleXMLElement;
use RuntimeException;

/**
 * Inputgroup
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Inputgroup
{
  /**
   * Inputgroup ID
   * 
   * @var    string
   */
  protected $name;
  
  /**
   * Fieldname
   * 
   * @var    string
   */
  protected $fieldname;
  
  /**
   * Inputgroup classname
   * 
   * @var    string
   */
  protected $classname;
  
  /**
   * Field label
   * 
   * @var    array
   */
  protected $label;
  
  /**
   * Field tip
   * 
   * @var    string
   */
  protected $tip;
  
  /**
   * Tip before field
   * 
   * @var    bool
   */
  protected $tipBefore;
  
  /**
   * Parts
   * 
   * @var    array
   */
  protected $parts;
  
  protected static $instances;
  
  public static function create(string $name)
  {
    if ( !isset(self::$instances) ){
      self::$instances = [];
    }
    if ( !isset(self::$instances[$name]) ){
      self::$instances[$name] = new self($name);
    }
    return self::$instances[$name];
  }
  
  public static function exists(string $name): bool
  {
    if ( !isset(self::$instances) ){
      self::$instances = [];
    }
    return isset(self::$instances[$name]);
  }
  
  public function __construct(string $name)
  {
    $this->name = $name;
  }
  
  public function setFieldname(string $fieldname)
  {
    $this->fieldname = $fieldname;
    return $this;
  }
  
  public function setClassname(string $classname)
  {
    $this->classname = $classname;
    return $this;
  }
  
  public function setLabel(array $label)
  {
    $this->label = $label;
    return $this;
  }
  
  public function setTip(string $tip)
  {
    $this->tip = $tip;
    return $this;
  }
  
  public function setTipBefore(bool $tipBefore=true)
  {
    $this->tipBefore = $tipBefore;
    return $this;
  }
  
  public function addField(array $field)
  {
    $this->parts[] = (object)[
      'type' => 'field',
      'field' => $field,
    ];
    return $this;
  }
  
  public function addButton(string $text, string $classname='')
  {
    $this->parts[] = (object)[
      'type' => 'button',
      'text' => $text,
      'classname' => $classname,
    ];
    return $this;
  }
  
  public function addButtonShell(string $text, string $classname='')
  {
    $this->parts[] = (object)[
      'type' => 'buttonShell',
      'text' => $text,
      'classname' => $classname,
    ];
    return $this;
  }
  
  public function render(): array
  {
    $classes = [];
    if ( $this->classname ){
      foreach(explode(' ', $this->classname) as $className){
        if ( in_array($className, ['input-sm','input-lg']) ){
          $classes[] = $classname;
        }
      }
    }
    
    return [
      'type' => 'inputgroup',
      'classname' => implode(' ', $classes),
      'name' => $this->name,
      'fieldname' => $this->fieldname,
      'label' => $this->label,
      'tip' => $this->tip,
      'tipBefore' => $this->tipBefore,
      'parts' => $this->parts,
    ];
  }
}
