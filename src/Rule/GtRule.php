<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Exception\InvalidException;
use JDZ\Form\FormData;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class GtRule extends CompareRule
{
    public string $name = 'gt';
    public string $message = 'Field integer value <= Control integer value';

    public function execute(FieldInterface $field, FormData $data): void
    {
        if (false === $field->isEmpty()) {
            $value1 = (int) $data->get($field->getName());
            $value2 = (int) $data->get($this->compareTo);

            if ($value1 <= $value2) {
                throw new InvalidException($this->message);
            }
        }
    }
}
