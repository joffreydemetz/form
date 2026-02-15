<?php

declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Checkbox extends InputField
{
    public string $type = 'checkbox';
    public string $label = '';
    public string $tip = '';
    public bool $checked = false;
    public bool $immutable = true;
    protected string $renderer = 'checkbox';

    public function getValue(): mixed
    {
        return $this->value ?? ($this->checked ? 'on' : null);
    }

    public function setCheckboxValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function setCheckboxLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function setCheckboxTip(string $tip): static
    {
        $this->tip = $tip;
        return $this;
    }

    public function withChecked(bool $checked = true): static
    {
        $this->checked = $checked;
        return $this;
    }

    public function toData(): array
    {
        $data = parent::toData();

        $data['label'] = $this->label;
        $data['tip'] = $this->tip;

        return $data;
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        if (true === $this->checked) {
            $attrs['checked'] = 'checked';
        }

        return $attrs;
    }
}
