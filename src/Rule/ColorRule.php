<?php
declare(strict_types=1);

namespace JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class ColorRule extends PatternRule
{
    public string $name = 'color';
    public string $pattern = '^#[A-Fa-f0-9]{6}$';
    public string $message = 'Invalid hex color';
}
