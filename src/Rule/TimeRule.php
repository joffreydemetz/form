<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\FormError;
use JDZ\Form\Rule\PatternRule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class TimeRule extends PatternRule
{
    public string $name = 'time';
    public string $pattern = '^[0-9]{2}:[0-9]{2}(:[0-9]{2})?$';
    public string $message = 'Invalid time';
    public ?FormError $errorCode = FormError::INVALID_TIME;
}
