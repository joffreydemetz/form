<?php

declare(strict_types=1);

namespace JDZ\Form\FormRow;

class Radioboxes extends Checkboxes
{
    protected string $boxes = 'radio';
    protected bool $arrayName = false;
}
