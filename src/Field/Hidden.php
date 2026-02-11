<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Hidden extends InputField
{
    public string $type = 'hidden';

    public function toData(): array
    {
        $this->placeholder = '';
        $this->pattern = '';
        $this->autocomplete = '';
        $this->maxlength = 0;
        $this->required = false;
        $this->readonly = false;

        $data = parent::toData();

        $data['hidden'] = true;

        return $data;
    }
}
