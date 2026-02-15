<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\FormData;
use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Tel extends InputField
{
    public string $type = 'tel';
    public string $autocomplete = 'tel';
    public string $pattern = '^[0-9\-\.]{7,15}?$';
    public string $errorMessage = 'Invalid phone number';
    public int $maxlength = 15;

    public function init(): void
    {
        parent::init();

        $this->addFilter(
            new \JDZ\Form\Filter\TelFilter()
        );

        /* @TODO LOAD pattern with i18nPhone */
    }

    public function validate(FormData $data): bool
    {
        if (!isset($this->rules['tel'])) {
            $rule = new \JDZ\Form\Rule\TelRule($this->errorMessage);
            $rule->setPattern($this->pattern);
            $this->addRule($rule);
        }

        return parent::validate($data);
    }
}
