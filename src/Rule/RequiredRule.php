<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Exception\RequiredException;
use JDZ\Form\FormData;
use JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class RequiredRule extends Rule
{
    public string $name = 'required';
    public string $message = 'Field is required';

    public function execute(FieldInterface $field, FormData $data): void
    {
        if (true === $field->required && true === $field->isEmpty()) {
            throw new RequiredException($this->message);
        }
    }
}
