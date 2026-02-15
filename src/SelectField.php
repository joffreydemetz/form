<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\SelectFieldOptgroup;
use JDZ\Form\SelectFieldOption;

/**
 * Abstract Select field
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class SelectField extends Field
{
    protected string $renderer = 'select';
    public bool $multiple = false;
    public bool $disabledAtTheEnd = false;
    public int $size = 0;
    public array $options = [];
    public array $selected = [];

    public bool $nice = false;
    public bool $niceSearch = false;
    public string $nicePlaceholder = '';
    public string $niceSearchPlaceholder = '';

    public function __clone()
    {
        $this->multiple = false;
        $this->disabledAtTheEnd = false;
        $this->size = 0;
        $this->options = [];
        $this->selected = [];
        $this->nice = false;
        $this->niceSearch = false;
        $this->nicePlaceholder = '';
        $this->niceSearchPlaceholder = '';
    }

    public function setValue($value): static
    {
        $list = [];

        if (!$value) {
            $list = [];
        } elseif (true === $this->multiple) {
            $list = \is_array($value) ? $value : explode(',', $value);
        } else {
            $list = [$value];
        }

        $this->selected = $list;

        return parent::setValue($value);
    }

    public function setSize(int $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function setNicePlaceholder(string $nicePlaceholder): static
    {
        $this->nicePlaceholder = $nicePlaceholder;
        return $this;
    }

    public function setNiceSearchPlaceholder(string $niceSearchPlaceholder): static
    {
        $this->niceSearchPlaceholder = $niceSearchPlaceholder;
        return $this;
    }

    public function setOptions(array $options = []): static
    {
        $this->options = $options;
        return $this;
    }

    public function addOption($item): static
    {
        $this->options[] = $item;
        return $this;
    }

    public function hasOptions(): bool
    {
        // ignore if only empty option
        if (1 === count($this->options) && 0 === intval($this->options[0]->value)) {
            return false;
        }

        return count($this->options) > 0;
    }

    public function withMultiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function withDisabledAtTheEnd(bool $disabledAtTheEnd = true): static
    {
        $this->disabledAtTheEnd = $disabledAtTheEnd;
        return $this;
    }

    public function withNice(bool $niceSearch = false, string $nicePlaceholder = 'Choose', string $niceSearchPlaceholder = 'Search'): static
    {
        $this->nice = true;
        $this->niceSearch = $niceSearch;
        $this->setNicePlaceholder($nicePlaceholder);
        $this->setNiceSearchPlaceholder($niceSearchPlaceholder);
        return $this;
    }

    public function areDisabled(array $list): static
    {
        foreach ($this->options as $item) {
            $item->withDisabled(in_array($item->value, $list));
        }
        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->selected);
    }

    public function toData(): array
    {
        $data = parent::toData();

        $data['selected'] = $this->selected;
        $data['options'] = $this->renderOptions();

        return $data;
    }

    public function toStatic(): string
    {
        return $this->readableValue();
    }

    public function onFillValues(FormData $data): void
    {
        parent::onFillValues($data);

        $this->setValue($data->get($this->name));

        foreach ($this->options as $item) {
            if ($item instanceof SelectFieldOptgroup) {
                foreach ($item->options as $option) {
                    $option->withSelected(in_array($option->value, $this->selected));
                }
            } else {
                $item->withSelected(in_array($item->value, $this->selected));
            }
        }
    }

    protected function readableValue(): string
    {
        $readable = [];

        foreach ($this->options as $item) {
            if ($item instanceof SelectFieldOptgroup) {
                foreach ($item->options as $option) {
                    if (in_array($option->value, $this->selected)) {
                        $readable[] = $option->text;
                    }
                }
            } else {
                if (in_array($item->value, $this->selected)) {
                    $readable[] = $item->text;
                }
            }
        }

        return $readable ? implode(', ', $readable) : '';
    }

    protected function renderOptions(): array
    {
        $options = [];
        $disabled = [];
        foreach ($this->options as $item) {
            if ($item instanceof SelectFieldOptgroup) {
                foreach ($item->options as $option) {
                    if (true === $this->niceSearch && !isset($option->searchable)) {
                        $option->searchable = $option->text;
                    }
                    $option->withSelected(in_array($option->value, $this->selected));
                }
                $options[] = $item->toData();
                continue;
            }

            $item->withSelected(in_array($item->value, $this->selected));
            if (true === $this->niceSearch && null === $item->searchable && '' === $item->text) {
                $item->searchable = $item->text;
            }

            if (true === $this->disabledAtTheEnd && true === $item->disabled) {
                $disabled[] = $item->toData();
                continue;
            }

            $options[] = $item->toData();
        }

        if ($disabled) {
            $options = array_merge($options, $disabled);
        }

        return $options;
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        if ($this->size > 0) {
            $attrs['size'] = $this->size;
        }

        if (true == $this->multiple) {
            $attrs['multiple'] = 'multiple';
        }

        if (true === $this->nice) {
            $attrs['data-nice'] = true;

            if ($this->niceSearch) {
                $attrs['data-nice-search'] = true;

                if ($this->niceSearchPlaceholder) {
                    $attrs['data-nice-search-placeholder'] = $this->niceSearchPlaceholder;
                }
            }

            if ($this->nicePlaceholder) {
                $attrs['data-nice-placeholder'] = $this->nicePlaceholder;
            }
        }

        return $attrs;
    }
}
