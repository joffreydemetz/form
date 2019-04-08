<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Renderer;

use JDZ\Form\Field\FieldInterface;
/**
 * Form fieldset
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Fieldset 
{
  /**
   * Fieldset id attribute
   * 
   * @var    string   
   */
  protected $id;
  
  /**
   * Form active component
   * 
   * @var    string   
   */
  protected $component;
  
  /**
   * Fieldset name
   * 
   * @var    string   
   */
  protected $name;
  
  /**
   * Fieldset group
   * 
   * @var    string   
   */
  protected $group;
  
  /**
   * Fieldset legend
   * 
   * @var    string   
   */
  protected $legend;
  
  /**
   * Fieldset description
   * 
   * @var    string   
   */
  protected $description;
  
  /**
   * Is active tab (only for tab/accordion mode)
   * 
   * @var    bool   
   */
  protected $active = false;
  
  /**
   * Fieldset fields
   * 
   * @var    [FieldInterface]   
   */
  protected $fields = [];
  
  public static function create()
  {
    return new self();
  }
  
  public function setComponent(string $component)
  {
    $this->component = $component;
    return $this;
  }
  
  public function setGroup(string $group)
  {
    $this->group = $group;
    return $this;
  }
  
  public function setName(string $name)
  {
    $this->name = $name;
    return $this;
  }
  
  public function setLegend(string $legend)
  {
    $this->legend = $legend;
    return $this;
  }
  
  public function setDescription(string $description)
  {
    $this->description = $description;
    return $this;
  }
  
  public function setFields(array $fields)
  {
    $this->fields = $fields;
    return $this;
  }
  
  public function setActive(bool $active=true)
  {
    $this->active = $active;
    return $this;
  }
  
  public function setId(string $id)
  {
    $this->id = $id;
    return $this;
  }
  
  public function makeId()
  {
    if ( !$this->id ){
      $parts = [];
      $parts[] = 'tab';
      if ( $this->group ){
        $parts[] = $this->group;
      }
      if ( $this->name ){
        $parts[] = $this->name;
      }
      
      $this->id = implode('-', $parts);
    }
    
    return $this;
  }
  
  public function getComponent(): string
  {
    return $this->component;
  }
  
  public function getGroup(): string
  {
    return $this->group;
  }
  
  public function getName(): string
  {
    return $this->name;
  }
  
  public function getId(): string
  {
    return $this->id;
  }
  
  public function getLegend(): string
  {
    return $this->legend;
  }
  
  public function getDescription(): string
  {
    return $this->description;
  }
  
  public function getFields(): array
  {
    return $this->fields;
  }
  
  public function isActive(): bool
  {
    return $this->active;
  }
}
