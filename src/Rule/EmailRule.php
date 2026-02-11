<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Exception\InvalidException;
use JDZ\Form\FormData;
use JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class EmailRule extends Rule
{
    public string $name = 'email';
    public string $message = 'Invalid email address';

    public function execute(FieldInterface $field, FormData $data): void
    {
        if (false === $field->isEmpty()) {
            $value = $data->get($field->getName());

            if (false === strpos($value, '@') || !\filter_var($value, \FILTER_VALIDATE_EMAIL)) {
                throw new InvalidException($this->message);
            }
        }
    }
}
