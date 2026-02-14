<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Contract\FieldInterface;

abstract class Rule
{
    public string $name = 'rule';
    public string $message = '';
    public ?FormError $errorCode = null;

    public function __construct(?string $message = null, ?FormError $errorCode = null)
    {
        if ($message) {
            $this->message = $message;
        }
        if ($errorCode) {
            $this->errorCode = $errorCode;
        }
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    public function setErrorCode(FormError $errorCode)
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    abstract public function execute(FieldInterface $field, FormData $data): void;
}
