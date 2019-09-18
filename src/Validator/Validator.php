<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Form\Validator;

use JDZ\Form\Form;
use JDZ\Form\FormHelper;
use JDZ\Form\Exception\InvalidException;
use JDZ\Form\Exception\RequiredException;
use JDZ\Form\Field\Field;
use JDZ\Form\Rule\Rule;
use JDZ\Form\FormData;
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
  
  public function setForm(Form $form)
  {
    $this->form = $form;
    return $this;
  }
  
  public function execute(FormData $data, $group=null)
  {
    $fields = $this->form->findFieldsByGroup($group);
    
    if ( !$fields ){
      throw new RuntimeException('No fields found for '.$group);
    }
    
    $return = true;
    
    foreach($fields as $element){
      $attrs   = $element->xpath('ancestor::fields[@name]/@name');
      $groups  = array_map('strval', $attrs ? $attrs : []);
      $group   = implode('.', $groups);
      $name    = (string) $element['name'];
      
      $key = ($group===''?'':$group.'.').$name;
      
      $field = $this->form->getField($element, $group, $data->get($key, ''));
      $value = $data->get($key, '', $field->getFilter());
      
      if ( !($result=$this->check($field, $group, $value, $data)) ){
        $return = false;
      }
    }
    
    return $return;
  }
  
  /**
   * Test a field object based on field data
   * 
   * @param   Field       $field      Form Field instanse
   * @param   string      $group      The optional dot-separated form group path on which to find the field
   * @param   mixed       $value      The optional value to use as the default for the field
   * @param   FormData  $data       An optional Data object with the entire data set to validate against the entire form
   * @return  mixed       Boolean true if field value is valid, False or Exception on failure
   */
  protected function check(Field $field, $group=null, $value=null, $data=null)
  {
    $element = $field->getElement();
    $name    = (string)$element['name'];
    
    if ( $required = $field->isRequired() ){
      if ( $field->isEmpty() ){
        $message = FormHelper::getRequiredError($field->get('message'), $this->form->getComponent(), $name);
        $this->form->setError( new RequiredException($message), $field );
        return false;
      }
    }
    
    if ( $validate = $field->get('validate') ){
      foreach($validate as $type){
        // debugMe($type);
        // debugMe($this->form->getNs());
        foreach($this->form->getNs() as $ns){
          $Class = $ns.'\\Rule\\'.ucfirst($type);
          if ( class_exists($Class) ){
            break;
          }
        }
        // $Class = $this->form->getNs().'\\Rule\\'.ucfirst($type);
        
        if ( !class_exists($Class) ){
          throw new RuntimeException('Unrecognized rule type :: '.$type);
        }
        
        // debugMe($Class);
        $rule = new $Class();
        $rule
          ->setForm($this->form)
          ->setElement($element)
          ->setGroup($group)
          ->setData($data);
        
        if ( false === $rule->test($value) ){
          $message = FormHelper::getRuleError('', $this->form->getComponent(), $name, $type);
          $this->form->setError( new InvalidException($message), $field );
          return false;
        }        
      }
    }
    
    return true;
  }
}