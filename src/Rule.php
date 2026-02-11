<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Contract\FieldInterface;

abstract class Rule
{
    public string $name = 'rule';
    public string $message = '';

    public function __construct(?string $message = null)
    {
        if ($message) {
            $this->message = $message;
        }
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    abstract public function execute(FieldInterface $field, FormData $data): void;
}
