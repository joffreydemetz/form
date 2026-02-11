<?php
declare(strict_types=1);

namespace JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class TelRule extends PatternRule
{
    public string $name = 'tel';
    public string $pattern = '^[0-9\+\.\-\(\)]{7,15}?$';
    public string $message = 'Invalid phone number';
}
