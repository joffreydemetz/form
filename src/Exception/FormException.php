<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Exception;

use Exception;

/**
 * Form exception
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class FormException extends Exception
{
  /**
   * Magic method to get the string representation of this object
   * 
   * @return   string
   */
  public function __toString()
  {
    return $this->getMessage();
  }
}
