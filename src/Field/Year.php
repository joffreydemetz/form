<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\Field\Date;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Year extends Date
{
    public string $type = 'number';
    public string $pattern = '[0-9]{4}';

    protected string $formatInput = 'Y';
    protected string $formatOutput = 'Y';
    protected string $formatReadable = 'Y';
}
