<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Exception\InvalidException;
use JDZ\Form\FormData;
use JDZ\Form\FormError;
use JDZ\Form\Rule\CompareRule;
use Carbon\Carbon;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class DateLtRule extends CompareRule
{
    public string $name = 'dateLt';
    public string $message = 'Field date value >= Control date value';
    public ?FormError $errorCode = FormError::DATE_NOT_LT;

    public function execute(FieldInterface $field, FormData $data): void
    {
        if (false === $field->isEmpty()) {
            $value1 = $data->get($field->getName());
            $value2 = $data->get($this->compareTo);

            if ('' !== $value2) {
                $date1 = new Carbon($value1);
                $date2 = new Carbon($value2);

                if ($date1 >= $date2) {
                    throw new InvalidException($this->message, $this->errorCode);
                }
            }
        }
    }
}
