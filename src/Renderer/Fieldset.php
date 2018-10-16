<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Renderer;

/**
 * Form fieldset
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Fieldset 
{
  // use \JDZ\Utilities\Traits\Get,
      // \JDZ\Utilities\Traits\Set;
  
  /**
   * Fieldset id attribute
   * 
   * @var    Form   
   */
  protected $id;
  
  /**
   * Form active component
   * 
   * @var    string   
   */
  protected $component;
  
  /**
   * Fieldset group
   * 
   * @var    string   
   */
  protected $group;
  
  /**
   * Is active tab (only for tab/accordion mode)
   * 
   * @var    string   
   */
  protected $active;
  
  /**
   * Fieldset name
   * 
   * @var    string   
   */
  protected $name;
  
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
   * Fieldset fields
   * 
   * @var    array   
   */
  protected $fields;
  
  /**
   * Constructor
   * 
   * @param   array   $properties     Key/Value pairs of the fieldset properties
   */
  public function __construct(array $properties)
  {
    $this->component   = (string) $properties['component'];
    $this->group       = (string) $properties['group'];
    $this->active      = (bool) $properties['active'];
    $this->name        = (string) $properties['name'];
    $this->legend      = (string) $properties['label'];
    $this->description = (string) $properties['description'];
    $this->fields      = (array) $properties['fields'];
    $this->id          = 'tab-'.($this->group !== '' ? $this->group.'-'.$this->name : $this->name);
  }
  
  public function getComponent()
  {
    return $this->component;
  }
  
  public function getGroup()
  {
    return $this->group;
  }
  
  public function isActive()
  {
    return $this->active;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function getLegend()
  {
    return $this->legend;
  }
  
  public function getDescription()
  {
    return $this->description;
  }
  
  public function getFields()
  {
    return $this->fields;
  }
  
  public function getId()
  {
    return $this->id;
  }
}
