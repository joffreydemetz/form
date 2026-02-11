<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Exception\InvalidException;
use JDZ\Form\FormData;
use JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class PatternRule extends Rule
{
    public string $name = 'pattern';
    public string $pattern;

    public function setPattern(string $pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function execute(FieldInterface $field, FormData $data): void
    {
        if (false === $field->isEmpty()) {
            $value = $data->get($field->getName());

            if (!preg_match("/" . $this->pattern . "/", $value)) {
                throw new InvalidException($this->message);
            }
        }
    }
}
