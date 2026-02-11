<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Field;

/**
 * Abstract Textarea field
 *
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class TextareaField extends Field
{
    protected string $renderer = 'textarea';
    public string $placeholder = '';
    public int $maxlength = 0;
    public int $cols = 0;
    public int $rows = 0;

    public function __clone()
    {
        $this->placeholder = '';
        $this->maxlength = 0;
        $this->cols = 0;
        $this->rows = 0;
    }

    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function setMaxlength(int $maxlength)
    {
        $this->maxlength = $maxlength;
        return $this;
    }

    public function setCols(int $cols)
    {
        $this->cols = $cols;
        return $this;
    }

    public function setRows(int $rows)
    {
        $this->rows = $rows;
        return $this;
    }

    public function toData(): array
    {
        $data = parent::toData();

        $data['content'] = $this->getValue();

        return $data;
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

        if ('' !== $this->placeholder) {
            $attrs['placeholder'] = $this->placeholder;
        }

        if ($this->cols > 0) {
            $attrs['cols'] = $this->cols;
        }

        if ($this->rows > 0) {
            $attrs['rows'] = $this->rows;
        }

        if ($this->maxlength > 0) {
            $attrs['maxlength'] = $this->maxlength;
        }

        return $attrs;
    }
}
