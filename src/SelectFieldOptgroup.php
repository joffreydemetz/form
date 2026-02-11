<?php
declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Renderer\Renderable;

class SelectFieldOptgroup extends Renderable
{
    protected string $renderer = 'optgroup';
    public string $label;
    public bool $disabled;
    public array $options = [];

    public function __construct(string $label = '', bool $disabled = false)
    {
        $this->label = $label;
        $this->disabled = $disabled;
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options;
        return $this;
    }

    public function withDisabled(bool $disabled = true)
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function toData(): array
    {
        $data = parent::toData();

        $data['label'] = $this->label;

        $options = [];
        foreach ($this->options as $option) {
            $options[] = $option->toData();
        }

        $data['options'] = $options;

        return $data;
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        if ('' !== $this->label) {
            $attrs['label'] = $this->label;
        }

        if (true === $this->disabled) {
            $attrs['disabled'] = 'disabled';
        }

        return $attrs;
    }
}
