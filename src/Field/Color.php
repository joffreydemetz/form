<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;
use JDZ\Form\FormData;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Color extends InputField
{
    public string $type = 'color';
    public string $pattern = '^#[A-Fa-f0-9]{6}$';
    public string $errorMessage = 'Invalid HEX color';

    public function init()
    {
        parent::init();

        $this->addFilter(
            new \JDZ\Form\Filter\ColorFilter()
        );
    }

    public function setValue($value)
    {
        return parent::setValue($this->sanitizeInputValueColor($value));
    }

    public function validate(FormData $data)
    {
        if (!isset($this->rules['color'])) {
            $this->addRule(
                new \JDZ\Form\Rule\ColorRule($this->errorMessage)
            );
        }

        return parent::validate($data);
    }

    protected function sanitizeInputValueColor(?string $value)
    {
        if ($value) {
            if (!preg_match("/" . $this->pattern . "/", $value)) {
                $value = '';
            }
        } else {
            $value = '';
        }

        return $value;
    }
}
