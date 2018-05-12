<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Renderer;

/**
 * Form Renderer
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class SimpleRenderer extends Renderer
{
  protected $type = 'simple';
  
  /**
   * {@inheritDoc}
   */
  public function render(array $data=[])
  {
    $fieldsets = $this->getFieldsets();
    
    return parent::render(array_merge($data, [
      'fieldsets' => $fieldsets,
    ]));
  }
}