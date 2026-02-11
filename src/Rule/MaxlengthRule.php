<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\FormData;
use JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class MaxlengthRule extends Rule
{
    public string $name = 'maxlength';
    public string $message = 'Too many characters';

    public function execute(FieldInterface $field, FormData $data): void
    {
        if (false === $field->isEmpty()) {
            $value = $data->get($field->getName());

            if ($field->maxlength > 0) {
                if (strlen($value) > $field->maxlength) {
                    $value = substr($value, 0, $field->maxlength);
                    $field->setValue($value);
                    $data->set($field->getName(), $value);
                }
            }
        }
    }
}
