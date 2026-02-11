<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\Field\Date;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Week extends Date
{
    public string $type = 'week';
    public string $pattern = '[0-9]{4}W[0-9]{2}';

    protected string $formatOutput = 'Y\WW';
    protected string $formatReadable = 'Y \W-W';
}
