<?php

declare(strict_types=1);

namespace JDZ\Form\Exception;

use JDZ\Form\FormError;

class RuleException extends FormException
{
    public ?FormError $errorCode = null;

    public function __construct(string $message = '', ?FormError $errorCode = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->errorCode = $errorCode;
        parent::__construct($message, $code, $previous);
    }
}
