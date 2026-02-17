<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\FormError;
use JDZ\Form\Rule\PatternRule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class PasswordRule extends PatternRule
{
    public string $name = 'password';
    public string $message = 'Invalid password';
    public ?FormError $errorCode = FormError::INVALID_PASSWORD;
}
