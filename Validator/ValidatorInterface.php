<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form;

use JDZ\Form\Exception\ValidateException;

/**
 * Validator interface
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class ValidatorInterface 
{
	/**
	 * Validate form fields
   * 
	 * @param 	array     $data       Data to validate fields against
	 * @param 	string    $group      The optional dot-separated form group path on which to find the field
	 * @return 	bool      True if all fields valid
	 * @throw   ValidateException
	 */
	public function execute($data, $group=null);
}