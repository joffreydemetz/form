<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Contract\FormFieldsetInterface;
use JDZ\Form\Contract\FormRowInterface;
use JDZ\Form\Exception\FormException;
use JDZ\Renderer\Element;

class FormFieldset extends Element implements FormFieldsetInterface
{
    protected string $renderer = 'form.fieldset';

    public string $uid = '';
    public string $label = '';
    public string $description = '';
    public array $fields = [];
    protected int $currentFieldPosition = 0;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getField(string $name): FormRowInterface
    {
        if (!isset($this->fields[$name])) {
            throw new FormException('Field ' . $this->getName() . '.' . $name . ' not found in Fieldset ' . $this->getName());
        }

        return $this->fields[$name];
    }

    public function addField(FormRowInterface $field): FormRowInterface
    {
        $name = $field->getName();
        if (!isset($this->fields[$name])) {
            $field->setFieldset($this);
            $field->setPosition(++$this->currentFieldPosition);
            $this->fields[$name] = $field;
        }

        return $this->fields[$name];
    }

    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    public function removeField(string $name): static
    {
        if (isset($this->fields[$name])) {
            unset($this->fields[$name]);
        }
        return $this;
    }

    public function onPrepare(): bool
    {
        foreach ($this->fields as $field) {
            if (false === $field->onPrepare()) {
                $this->removeField($field->getName());
            }
        }

        return count($this->fields) > 0;
    }

    public function toData(): array
    {
        $data = parent::toData();

        $data['name'] = str_replace('.', '-', $this->getName());

        if ('' !== $this->label) {
            $data['label'] = $this->label;
        }

        if ('' !== $this->description) {
            $data['description'] = $this->description;
        }

        if ($this->fields) {
            $data['fields'] = $this->renderFields();
        }

        return $data;
    }

    public function setFieldPositions(array $fieldNames): static
    {
        $this->currentFieldPosition = 0;
        foreach ($fieldNames as $name) {
            $this->getField($name)
                ->setPosition(++$this->currentFieldPosition);
        }
        return $this;
    }

    public function setFieldPosition(string $positionFieldName, int|string $position, string $direction = 'before'): static
    {
        $positionKeys = [];
        foreach ($this->fields as $field) {
            $positionKeys[$field->getPosition()] = $field->getName();
        }
        ksort($positionKeys);

        if ('first' === $position) {
            $position = 1;
            $direction = 'before';
        } elseif ('last' === $position) {
            $position = count($this->fields);
            $direction = 'after';
        } else {
            $position = (int)$position;
        }

        $positionField = $this->getField($positionFieldName);

        $this->currentFieldPosition = 0;
        foreach ($positionKeys as $name) {
            $field = $this->getField($name);

            if ($field->getName() === $positionField->getName()) {
                continue;
            }

            if ('before' === $direction && $this->currentFieldPosition + 1 === $position) {
                $positionField->setPosition(++$this->currentFieldPosition);
            }

            $field->setPosition(++$this->currentFieldPosition);

            if ('after' === $direction && $this->currentFieldPosition + 1 === $position) {
                $positionField->setPosition(++$this->currentFieldPosition);
            }
        }

        return $this;
    }

    public function setFieldPositionAfter(string $positionFieldName, string $offsetFieldName): static
    {
        return $this->setFieldPosition($positionFieldName, $this->getField($offsetFieldName)->getPosition(), 'after');
    }

    public function setFieldPositionBefore(string $positionFieldName, string $offsetFieldName): static
    {
        return $this->setFieldPosition($positionFieldName, $this->getField($offsetFieldName)->getPosition(), 'before');
    }

    protected function renderAttrs(): array
    {
        array_unshift($this->styles, 'form-fieldset');

        $this->addDataAttr('name', $this->getName());

        $attrs = parent::renderAttrs();

        return $attrs;
    }

    protected function renderFields(): array
    {
        $fields = [];
        $fieldsNotPositioned = [];
        foreach ($this->fields as $field) {
            if (0 === $field->getPosition()) {
                $fieldsNotPositioned[] = $field;
                continue;
            }
            $fields[$field->getPosition()] = $field;
        }

        ksort($fields);

        $fields = array_merge(array_values($fields), array_values($fieldsNotPositioned));

        $namedFields = [];
        foreach ($fields as $field) {
            $namedFields[$field->getName()] = $field->toData();
        }

        return $namedFields;
    }
}
