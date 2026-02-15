<?php

declare(strict_types=1);

namespace JDZ\Form\FormRow;

use JDZ\Form\Field;
use JDZ\Form\Field\Checkbox;
use JDZ\Form\FormData;
use JDZ\Form\FormError;
use JDZ\Form\FormRow;
use JDZ\Form\FormValidationError;

class Checkboxes extends FormRow
{
    protected string $renderer = 'checkboxes';
    public string $boxes = 'checkbox';
    public bool $vertical = false;
    public bool $arrayName = true;
    public bool $disabledAtTheEnd = false;
    public int $nbCols = 1;
    public int $maxSelection = 0;
    public array $list = [];
    public array $checkedList = [];

    public function setNbCols(int $nbCols = 1): static
    {
        $this->nbCols = $nbCols;
        return $this;
    }

    public function setList(array $list = []): static
    {
        $this->list = $list;
        return $this;
    }

    public function setMaxSelection(int $maxSelection = 0): static
    {
        $this->maxSelection = $maxSelection;
        return $this;
    }

    public function areDisabled(array $list = []): static
    {
        foreach ($this->list as $item) {
            $item->withDisabled(in_array($item->value, $list));
        }
        return $this;
    }

    public function withDisabledAtTheEnd(bool $disabledAtTheEnd = true): static
    {
        $this->disabledAtTheEnd = $disabledAtTheEnd;
        return $this;
    }

    public function withVertical(bool $vertical = true): static
    {
        $this->vertical = $vertical;
        return $this;
    }

    public function addItem(Checkbox $item): static
    {
        $item
            ->setPrefix($this->prefix)
            ->withArrayName($this->arrayName);

        $this->list[] = $item;
        return $this;
    }

    public function onFillValues(FormData $data): void
    {
        $checked = $data->get($this->getName());

        if (!$checked) {
            $checked = [];
        } elseif (!is_array($checked)) {
            $checked = explode(',', $checked);
        }

        $this->checkedList = array_values($checked);

        if (count($this->checkedList) > 1 && false === $this->arrayName) {
            $this->checkedList = [$this->checkedList[0]];
        }

        foreach ($this->list as $item) {
            $item->withChecked(in_array($item->value, $this->checkedList));
        }
    }

    public function validate(FormData $data): bool
    {
        if (true === $this->required && !$this->checkedList) {
            $label = $this->labelText ? '<strong>' . $this->labelText . '</strong>: ' : '';
            $this->errors[] = new FormValidationError(
                FormError::REQUIRED,
                $label . $this->getRequiredErrorMessage()
            );
            return false;
        }

        return true;
    }

    public function toData(): array
    {
        if ('' !== $this->prefix) {
            foreach ($this->list as $item) {
                if ($item instanceof Field) {
                    $item->setPrefix($this->prefix);
                }
            }
        }

        $data = parent::toData();

        if (true === $this->vertical) {
            $data['vertical'] = true;
        }

        if ($this->nbCols > 1) {
            $data['nbCols'] = $this->nbCols;
        }

        if ($this->maxSelection > 0) {
            $data['maxSelection'] = $this->maxSelection;
        }

        $data['boxes'] = $this->boxes;
        $data['list'] = $this->toDataList();

        return $data;
    }

    protected function getRequiredErrorMessage(): string
    {
        return 'This field is required';
    }

    protected function toDataList(): array
    {
        $list = [];
        $disabled = [];
        foreach ($this->list as $item) {
            if (true === $this->disabledAtTheEnd && true === $item->disabled) {
                $disabled[] = $item->toData();
                continue;
            }

            $list[] = $item->toData();
        }

        if ($disabled) {
            $list = array_merge($list, $disabled);
        }

        return $list;
    }

    protected function toStatic(): string
    {
        return $this->readableValue();
    }

    protected function readableValue(): string
    {
        $readable = [];

        foreach ($this->list as $item) {
            if (in_array($item->value, $this->checkedList)) {
                $readable[] = $item->label;
            }
        }

        return $readable ? implode(', ', $readable) : '';
    }

    protected function renderAttrs(): array
    {
        if (true === $this->vertical) {
            array_unshift($this->styles, 'vertical');
        } else {
            if ($this->nbCols > 1) {
                $this->dataAttrs['cols'] = $this->nbCols;
            }
            if ($this->maxSelection > 0) {
                $this->dataAttrs['max-selection'] = $this->maxSelection;
            }
        }

        if (false === $this->readonly) {
            $this->addStyle($this->boxes);
        }

        return parent::renderAttrs();
    }

    protected function bubbleFieldConfig(): void
    {
        if (true === $this->arrayName && $this->list) {
            foreach ($this->list as $item) {
                if ($item instanceof Field) {
                    $item->withArrayName();
                }
            }
        }
    }
}
