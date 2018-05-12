<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Validator;

use JDZ\Form\Form;
use JDZ\Form\Exception\ValidateException;
use JDZ\Form\FormData;

/**
 * Validator interface
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface ValidatorInterface 
{
  /**
   * Set the form singleton
   * 
   * @param  Form  $form  Form instance
   * @return $this
   */
  public function setForm(Form $form);
  
  /**
   * Validate form fields
   * 
   * @param   FormData $data   Data to validate fields against
   * @param   string     $group  The optional dot-separated form group path on which to find the field
   * @return  bool       True if all fields valid
   * @throw   ValidateException
   */
  public function execute(FormData $data, $group=null);
}