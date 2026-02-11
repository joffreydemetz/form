<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\FormData;
use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Email extends InputField
{
    public string $type = 'email';
    public string $autocomplete = 'email';
    public string $placeholder = '';
    public string $errorMessage = 'Invalid email address';

    public function init()
    {
        parent::init();

        $this->addFilter(
            new \JDZ\Form\Filter\EmailFilter()
        );
    }

    public function validate(FormData $data)
    {
        if (false === $this->noCheck && !isset($this->rules['email'])) {
            $rule = new \JDZ\Form\Rule\EmailRule($this->errorMessage);
            $this->addRule($rule);
        }

        return parent::validate($data);
    }
}
