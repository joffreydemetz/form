<?php
declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Renderer\Renderable;

class SelectFieldOption extends Renderable
{
    protected string $renderer = 'option';
    public string $value;
    public string $text;
    public ?string $searchable = null;
    public bool $selected;
    public bool $disabled;

    public function __construct(string $value = '', string $text = '', bool $selected = false, bool $disabled = false)
    {
        $this->value = $value;
        $this->text = $text;
        $this->selected = $selected;
        $this->disabled = $disabled;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function withSelected(bool $selected = true): static
    {
        $this->selected = $selected;
        return $this;
    }

    public function withDisabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function setText(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function setSearchable(string $searchable): static
    {
        $this->searchable = $searchable;
        return $this;
    }

    public function toData(): array
    {
        $data = parent::toData();

        $data['selected'] = $this->selected;
        $data['disabled'] = $this->disabled;
        $data['text'] = $this->text;

        return $data;
    }

    protected function renderAttrs(): array
    {
        if ($this->searchable) {
            $this->addDataAttr('display', $this->searchable);
        }

        $attrs = parent::renderAttrs();

        $attrs['value'] = $this->value;

        if (true === $this->selected) {
            $attrs['selected'] = 'selected';
        }

        if (true === $this->disabled) {
            $attrs['disabled'] = 'disabled';
        }

        return $attrs;
    }
}
