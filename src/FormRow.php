<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Contract\FormFieldsetInterface;
use JDZ\Form\Contract\FormRowInterface;
use JDZ\Renderer\Element;

class FormRow extends Element implements FormRowInterface
{
    protected string $uid = '';
    protected string $prefix = '';
    protected string $labelText = '';
    protected string $tip = '';
    protected string $fieldContainerClass = 'form-field';
    protected bool $label = true;
    protected bool $tipOnTop = false;
    protected bool $required = false;
    protected bool $disabled = false;
    protected bool $readonly = false;
    protected bool $static = false;
    protected bool $labelOnTop = false;
    protected bool $arrayName = false;
    protected bool $offset = false;
    protected array $errors = [];
    protected ?FormFieldsetInterface $fieldset = null;
    protected ?FieldInterface $field = null;

    protected string $renderer = 'form.field';

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function getRenderer(): string
    {
        return $this->renderer;
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getLabelText(): string
    {
        return $this->labelText;
    }

    public function getTip(): string
    {
        return $this->tip;
    }

    public function hasLabel(): bool
    {
        return $this->label;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }

    public function isLabelOnTop(): bool
    {
        return $this->labelOnTop;
    }

    public function hasArrayName(): bool
    {
        return $this->arrayName;
    }

    public function hasOffset(): bool
    {
        return $this->offset;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFieldset(): ?FormFieldsetInterface
    {
        return $this->fieldset;
    }

    public function getField(): ?FieldInterface
    {
        return $this->field;
    }

    public function setFieldset(FormFieldsetInterface $fieldset): static
    {
        $this->fieldset = $fieldset;
        return $this;
    }

    public function setLabelText(string $labelText): static
    {
        $this->labelText = $labelText;
        return $this;
    }

    public function setPrefix(string $prefix): static
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setTip(string $tip = ''): static
    {
        $this->tip = $tip;
        return $this;
    }

    public function setField(?FieldInterface $field = null): static
    {
        $this->field = $field;
        return $this;
    }

    public function setValue($value): static
    {
        if ($this->field) {
            $this->field->setValue($value);
        }
        return $this;
    }

    public function setPosition(int $position): static
    {
        parent::setPosition($position);
        return $this;
    }

    public function setFormRowPosition(int|string $position, string $direction = 'before'): static
    {
        $this->fieldset->setFormRowPosition($this->getName(), $position, $direction);
        return $this;
    }

    public function setFormRowPositionAfter(string $offsetFieldName): static
    {
        if ($this->fieldset) {
            $this->setFormRowPosition($this->fieldset->getFormRow($offsetFieldName)->getPosition() + 1, 'after');
        }
        return $this;
    }

    public function setFormRowPositionBefore(string $offsetFieldName): static
    {
        if ($this->fieldset) {
            $this->setFormRowPosition($this->fieldset->getFormRow($offsetFieldName)->getPosition(), 'before');
        }
        return $this;
    }

    public function withOffset(bool $offset = true): static
    {
        $this->offset = $offset;
        return $this;
    }

    public function withLabel(bool $label = true): static
    {
        $this->label = $label;
        return $this;
    }

    public function withLabelOnTop(bool $labelOnTop = true): static
    {
        $this->labelOnTop = $labelOnTop;
        return $this;
    }

    public function withTipOnTop(bool $tipOnTop = true): static
    {
        $this->tipOnTop = $tipOnTop;
        return $this;
    }

    public function withRequired(bool $required = true): static
    {
        $this->required = $required;
        return $this;
    }

    public function withDisabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function withReadonly(bool $readonly = true): static
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function withStatic(bool $static = true): static
    {
        $this->static = $static;
        return $this;
    }

    public function withArrayName(bool $arrayName = true): static
    {
        $this->arrayName = $arrayName;
        return $this;
    }

    public function onFillValues(FormData $data): void
    {
        if ($this->field) {
            $this->field->onFillValues($data);
        }
    }

    public function onPrepare(): bool
    {
        $this->static = true === $this->disabled || ($this->field && true === $this->field->disabled);
        return true;
    }

    public function filter(FormData $data): static
    {
        if ($this->field) {
            $this->bubbleFieldConfig();

            $this->field->filter($data);
        }

        return $this;
    }

    public function validate(FormData $data): bool
    {
        if ($this->field) {
            $this->bubbleFieldConfig();

            if (false === $this->field->validate($data)) {
                $label = $this->labelText ? '<strong>' . $this->labelText . '</strong>: ' : '';
                foreach ($this->field->errors as $error) {
                    if ($error instanceof FormValidationError) {
                        $this->errors[] = new FormValidationError(
                            $error->code,
                            $label . $error->message
                        );
                    } else {
                        $this->errors[] = $label . $error;
                    }
                }
                return false;
            }
        }

        return true;
    }

    public function toData(): array
    {
        $this->bubbleFieldConfig();

        if (true === $this->labelOnTop) {
            $this->styles[] = 'label-on-top';
        }

        if (true === $this->required) {
            $this->styles[] = 'required';
        }

        if (true === $this->disabled) {
            $this->styles[] = 'disabled';
        }

        if (true === $this->readonly) {
            $this->styles[] = 'readonly';
        }

        $data = parent::toData();

        $data['name'] = $this->getName();

        if (true === $this->static) {
            $data['static'] = true;

            if (true === $this->readonly) {
                $data['staticValue'] = $this->toStatic();
            }
        }

        if (true === $this->required) {
            $data['required'] = true;
        }

        if (true === $this->disabled) {
            $data['disabled'] = true;
        }

        if (true === $this->readonly) {
            $data['readonly'] = true;
        }

        if (true === $this->label && '' !== $this->labelText) {
            $data['label'] = $this->labelText;
        } elseif (true === $this->offset) {
            $data['offsetField'] = true;
        }

        if ('' !== $this->tip) {
            $data['tip'] = $this->tip;
        }

        $data['field'] = $this->renderField();
        $data['prefix'] = $this->prefix;

        return $data;
    }

    protected function bubbleFieldConfig(): void
    {
        if ($this->field) {
            if (true === $this->arrayName) {
                $this->field->withArrayName();
            }

            if ('' !== $this->prefix) {
                $this->field->setPrefix($this->prefix);
            }

            if (true === $this->required) {
                $this->field->withRequired($this->required);
            }

            if (true === $this->disabled) {
                $this->field->withDisabled($this->disabled);
            }

            if (true === $this->readonly) {
                $this->field->withReadonly($this->readonly);
            }

            if (false === $this->label) {
                if ('' !== $this->labelText) {
                    $this->field->addAriaAttr('label', $this->labelText);
                }
            }
        }
    }

    protected function toStatic(): ?string
    {
        if ($this->field) {
            return $this->field->toStatic();
        }

        return '';
    }

    protected function renderAttrs(): array
    {
        if (true === $this->tipOnTop) {
            array_unshift($this->styles, 'tipBefore');
        }

        if (true === $this->static) {
            array_unshift($this->styles, 'static');
        }

        if ('' !== $this->fieldContainerClass) {
            array_unshift($this->styles, $this->fieldContainerClass);
        }

        $this->addDataAttr('id', $this->getName());

        $attrs = parent::renderAttrs();

        return $attrs;
    }

    protected function renderField(): ?array
    {
        return $this->field ? $this->field->toData() : null;
    }
}
