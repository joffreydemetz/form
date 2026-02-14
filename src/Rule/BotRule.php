<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Exception\InvalidException;
use JDZ\Form\FormData;
use JDZ\Form\FormError;
use JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class BotRule extends Rule
{
    public string $name = 'bot';
    public string $message = 'No bots allowed';
    public ?FormError $errorCode = FormError::BOT_DETECTED;

    public function execute(FieldInterface $field, FormData $data): void
    {
        if (false === $field->isEmpty()) {
            throw new InvalidException($this->message, $this->errorCode);
        }
    }
}
