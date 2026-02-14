<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Contract\FormFieldsetInterface;
use JDZ\Form\Contract\FormRowInterface;
use JDZ\Renderer\Element;

class FormRow extends Element implements FormRowInterface
{
    public string $uid = '';
    public string $prefix = '';
    public string $labelText = '';
    public string $tip = '';
    public string $fieldContainerClass = 'form-field';
    public bool $label = true;
    public bool $tipOnTop = false;
    public bool $required = false;
    public bool $disabled = false;
    public bool $readonly = false;
    public bool $static = false;
    public bool $labelOnTop = false;
    public bool $arrayName = false;
    public bool $offset = false;
    public array $errors = [];
    public ?FormFieldsetInterface $fieldset = null;
    public ?FieldInterface $field = null;

    protected string $renderer = 'form.field';

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function getRenderer(): string
    {
        return $this->renderer;
    }

    public function setFieldset(FormFieldsetInterface $fieldset)
    {
        $this->fieldset = $fieldset;
        return $this;
    }

    public function setLabelText(string $labelText)
    {
        $this->labelText = $labelText;
        return $this;
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setTip(string $tip = '')
    {
        $this->tip = $tip;
        return $this;
    }

    public function setField(?FieldInterface $field = null)
    {
        $this->field = $field;
        return $this;
    }

    public function setValue($value)
    {
        if ($this->field) {
            $this->field->setValue($value);
        }
        return $this;
    }

    public function setFieldPosition(int|string $position, string $direction = 'before')
    {
        $this->fieldset->setFieldPosition($this->getName(), $position, $direction);
        return $this;
    }

    public function setFieldPositionAfter(string $offsetFieldName)
    {
        return $this->fieldset && $this->setFieldPosition($this->fieldset->getField($offsetFieldName)->getPosition() + 1, 'after');
    }

    public function setFieldPositionBefore(string $offsetFieldName)
    {
        return $this->fieldset && $this->setFieldPosition($this->fieldset->getField($offsetFieldName)->getPosition(), 'before');
    }

    public function withOffset(bool $offset = true)
    {
        $this->offset = $offset;
        return $this;
    }

    public function withLabel(bool $label = true)
    {
        $this->label = $label;
        return $this;
    }

    public function withLabelOnTop(bool $labelOnTop = true)
    {
        $this->labelOnTop = $labelOnTop;
        return $this;
    }

    public function withTipOnTop(bool $tipOnTop = true)
    {
        $this->tipOnTop = $tipOnTop;
        return $this;
    }

    public function withRequired(bool $required = true)
    {
        $this->required = $required;
        return $this;
    }

    public function withDisabled(bool $disabled = true)
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function withReadonly(bool $readonly = true)
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function withArrayName(bool $arrayName = true)
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

    public function filter(FormData $data)
    {
        if ($this->field) {
            $this->bubbleFieldConfig();

            $this->field->filter($data);
        }

        return $this;
    }

    public function validate(FormData $data)
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

    protected function renderField()
    {
        return $this->field ? $this->field->toData() : null;
    }
}
