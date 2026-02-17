<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\FormError;
use JDZ\Form\Rule\PatternRule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class DateRule extends PatternRule
{
    public string $name = 'date';
    public string $pattern = '^([0-9]{4}-[0-9]{2}-[0-9]{2})(\s+[0-9]{2}:[0-9]{2}(:[0-9]{2})?)?$';
    public string $message = 'Invalid date';
    public ?FormError $errorCode = FormError::INVALID_DATE;
}
