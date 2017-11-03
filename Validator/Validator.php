<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form;

use JDZ\Form\Exception\InvalidException;
use JDZ\Form\Exception\RequiredException;
use JDZ\Form\Field\Field;
use JDZ\Form\Rule\Rule;
use JDZ\Registry\Registry;
use RuntimeException;

/**
 * Validator
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Validator implements ValidatorInterface
{
	/**
   * Form instance
   * 
	 * @var    Form   
	 */
  protected $form;
  
	/**
	 * Constructor
   * 
	 * @param 	Form      $form       Form instance
	 */
  public function __construct(Form &$form)
  {
    $this->form =& $form;
  }
  
	/**
	 * {@inheritDoc}
	 */
	public function execute($data, $group=null)
	{
		$fields = $this->form->findFieldsByGroup($group);
    
		if ( !$fields ){
      throw new RuntimeException('No fields found for '.$group);
		}
    
		$registry = new Registry($data);
    
    $return = true;
    
		foreach($fields as $element){
			$attrs   = $element->xpath('ancestor::fields[@name]/@name');
			$groups  = array_map('strval', $attrs ? $attrs : []);
			$group   = implode('.', $groups);
      $name    = (string) $element['name'];
      
      $key = ($group===''?'':$group.'.').$name;
      
      $field = Helper::loadField($this->form, $element, $group, $registry->get($key, '', 'raw'));
      
      $value = $registry->get($key, '', $field->get('filter', 'raw'));
      
			if ( !($result=$this->check($field, $group, $value, $registry)) ){
        $return = false;
			}
		}
    
		return $return;
	}
  
	/**
	 * Test a field object based on field data.
   * 
	 * @param 	Field     $field      Form Field instanse.
	 * @param 	string    $group      The optional dot-separated form group path on which to find the field.
	 * @param 	mixed     $value      The optional value to use as the default for the field.
	 * @param 	object    $input      An optional Registry object with the entire data set to validate
	 *                                against the entire form.
	 * @return 	mixed   Boolean true if field value is valid, False or Exception on failure.
	 */
	protected function check(Field $field, $group=null, $value=null, $input=null)
	{
    $element = $field->get('element');
    $ns      = $field->get('form')->getContext();
    $name    = (string)$element['name'];
    
		if ( $required = $field->get('required') ){
			if ( $field->isEmpty() ){
        $message = Helper::getRequiredError($field->get('message'), $ns, $name);
        $field->get('form')->setError( new RequiredException($message) );
        return false;
			}
		}
    
		if ( $validate = $field->get('validate') ){
      foreach($validate as $type){
        $rule = Rule::getInstance($type);
        
        if ( $rule === false ){
          throw new RuntimeException('Missing field rule ('.$type.') ['.get_class($this).']');
        }
        
        $form  = $field->get('form');
        $valid = $rule->test($element, $value, $group, $input, $form);
        
        if ( $valid === false ){
          $message = Helper::getRuleError('', $ns, $name, $type);
          $field->get('form')->setError( new InvalidException($message) );
          return false;
        }        
      }
		}
    
		return true;
	}
}