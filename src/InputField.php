<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Field;
use JDZ\Form\FormData;
use JDZ\Form\Rule\MaxlengthRule;

/**
 * Abstract Input field
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class InputField extends Field
{
    public string $type = 'text';
    public string $placeholder = '';
    public string $pattern = '';
    public int $maxlength = 0;
    protected string $renderer = 'input';

    public function __clone()
    {
        $this->placeholder = '';
        $this->pattern = '';
        $this->position = 0;
        $this->value = null;
        $this->default = '';
    }

    public function setPlaceholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function setPattern(string $pattern): static
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function setMaxlength(int $maxlength): static
    {
        $this->maxlength = $maxlength;
        return $this;
    }

    public function validate(FormData $data): bool
    {
        if ($this->maxlength > 0 && !isset($this->rules['maxlength'])) {
            \array_unshift($this->rules, new MaxlengthRule('This field exceeds the allowed length'));
        }
        return parent::validate($data);
    }

    public function toStatic(): string
    {
        $value = parent::toStatic();
        if ('' === $value && $this->placeholder) {
            $value = $this->placeholder;
        }
        return $value;
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        $attrs['type'] = $this->type;
        $attrs['value'] = $this->value ? \htmlspecialchars((string)$this->value, \ENT_COMPAT, 'UTF-8') : '';

        if ('' !== $this->placeholder) {
            $attrs['placeholder'] = $this->placeholder;
        }

        if ('' !== $this->pattern) {
            $attrs['pattern'] = $this->pattern;
        }

        if ($this->maxlength > 0) {
            $attrs['maxlength'] = $this->maxlength;
        }

        return $attrs;
    }
}
