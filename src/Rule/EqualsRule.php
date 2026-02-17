<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Exception\InvalidException;
use JDZ\Form\FormData;
use JDZ\Form\FormError;
use JDZ\Form\Rule\CompareRule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class EqualsRule extends CompareRule
{
    public string $name = 'equals';
    public string $message = 'Values do not match';
    public ?FormError $errorCode = FormError::VALUES_NOT_EQUAL;

    public function execute(FieldInterface $field, FormData $data): void
    {
        if (false === $field->isEmpty()) {
            $value1 = $data->get($field->getName());
            $value2 = $data->get($this->compareTo);

            if ($value1 !== $value2) {
                throw new InvalidException($this->message, $this->errorCode);
            }
        }
    }
}
