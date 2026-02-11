<?php
declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Renderer\Button;

class FormButton extends Button
{
    public function __clone()
    {
        parent::__clone();
    }

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->addStyle('btn');
    }
}
