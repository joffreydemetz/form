<?php

declare(strict_types=1);

namespace JDZ\Form;

class FormValidationError implements \Stringable
{
    public readonly FormError $code;
    public readonly string $message;

    public function __construct(FormError $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
