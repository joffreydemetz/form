<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\Field\Date;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Month extends Date
{
    public string $type = 'month';
    public string $pattern = '[0-9]{4}-[0-9]{2}';

    protected string $formatOutput = 'Y-m';
    protected string $formatReadable = 'm/Y';
}
