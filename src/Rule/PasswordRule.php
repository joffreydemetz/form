<?php
declare(strict_types=1);

namespace JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class PasswordRule extends PatternRule
{
    public string $name = 'password';
    public string $message = 'Invalid password';
}
