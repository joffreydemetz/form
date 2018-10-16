<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Field;

/**
 * Abstract Container (not a field but a div to display content)
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class ContainerField extends Field
{
  protected $content = '';
  
  protected function initDefinition()
  {
    parent::initDefinition();
    
    $this->defAttribute('content', '');
  }
  
  protected function initObject()
  {
    parent::initObject();
    
    $this->content = (string) $this->element['content'];
  }
  
  protected function renderField(array $attrs=[])
  {
    return array_merge(parent::renderField($attrs), [
      'type'    => 'container',
      'attrs'   => [
        'data-id' => $this->id,
      ],
      'content' => $this->getContainerContent(),
    ]);
  }
  
  protected function getContainerContent()
  {
    return $this->content;
  }
}
