<?php
declare(strict_types=1);

namespace JDZ\Form\FormRow;

use JDZ\Form\FormRow;

class Hidden extends FormRow
{
    protected string $renderer = 'form.hidden';
    public bool $label = false;
}
