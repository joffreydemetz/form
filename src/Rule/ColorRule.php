<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\FormError;
use JDZ\Form\Rule\PatternRule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class ColorRule extends PatternRule
{
    public string $name = 'color';
    public string $pattern = '^#[A-Fa-f0-9]{6}$';
    public string $message = 'Invalid hex color';
    public ?FormError $errorCode = FormError::INVALID_COLOR;
}
