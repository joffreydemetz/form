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

    protected string $uid = '';
    protected string $label = '';
    protected string $description = '';
    protected array $formRows = [];
    protected int $currentFieldPosition = 0;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFormRows(): array
    {
        return $this->formRows;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;
        return $this;
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

    public function getFormRow(string $name): FormRowInterface
    {
        if (!isset($this->formRows[$name])) {
            throw new FormException('Field ' . $this->getName() . '.' . $name . ' not found in Fieldset ' . $this->getName());
        }

        return $this->formRows[$name];
    }

    public function addFormRow(FormRowInterface $field): FormRowInterface
    {
        $name = $field->getName();
        if (!isset($this->formRows[$name])) {
            $field->setFieldset($this);
            $field->setPosition(++$this->currentFieldPosition);
            $this->formRows[$name] = $field;
        }

        return $this->formRows[$name];
    }

    public function hasFormRow(string $name): bool
    {
        return isset($this->formRows[$name]);
    }

    public function removeFormRow(string $name): static
    {
        if (isset($this->formRows[$name])) {
            unset($this->formRows[$name]);
        }
        return $this;
    }

    public function onPrepare(): bool
    {
        foreach ($this->formRows as $field) {
            if (false === $field->onPrepare()) {
                $this->removeFormRow($field->getName());
            }
        }

        return count($this->formRows) > 0;
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

        if ($this->formRows) {
            $data['fields'] = $this->renderFields();
        }

        return $data;
    }

    public function setFormRowPositions(array $fieldNames): static
    {
        $this->currentFieldPosition = 0;
        foreach ($fieldNames as $name) {
            $this->getFormRow($name)
                ->setPosition(++$this->currentFieldPosition);
        }
        return $this;
    }

    public function setFormRowPosition(string $positionFieldName, int|string $position, string $direction = 'before'): static
    {
        $positionKeys = [];
        foreach ($this->formRows as $field) {
            $positionKeys[$field->getPosition()] = $field->getName();
        }
        ksort($positionKeys);

        if ('first' === $position) {
            $position = 1;
            $direction = 'before';
        } elseif ('last' === $position) {
            $position = count($this->formRows);
            $direction = 'after';
        } else {
            $position = (int)$position;
        }

        $positionField = $this->getFormRow($positionFieldName);

        $this->currentFieldPosition = 0;
        foreach ($positionKeys as $name) {
            $field = $this->getFormRow($name);

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

    public function setFormRowPositionAfter(string $positionFieldName, string $offsetFieldName): static
    {
        return $this->setFormRowPosition($positionFieldName, $this->getFormRow($offsetFieldName)->getPosition(), 'after');
    }

    public function setFormRowPositionBefore(string $positionFieldName, string $offsetFieldName): static
    {
        return $this->setFormRowPosition($positionFieldName, $this->getFormRow($offsetFieldName)->getPosition(), 'before');
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
        foreach ($this->formRows as $field) {
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
