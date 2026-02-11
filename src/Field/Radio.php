<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\Field\Checkbox;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Radio extends Checkbox
{
    protected string $renderer = 'radio';
    public string $type = 'radio';
}
