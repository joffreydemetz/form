<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\FormData;
use JDZ\Form\FormError;
use JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class CallbackRule extends Rule
{
    public string $name = 'condition';
    public string $message = 'Condition failed';
    public ?FormError $errorCode = FormError::CALLBACK_FAILED;
    public $callback;

    public function setCallback(callable $callback): static
    {
        $this->callback = $callback;
        return $this;
    }

    public function execute(FieldInterface $field, FormData $data): void
    {
        ($this->callback)($field, $data, $this->message, $this->errorCode);
    }
}
