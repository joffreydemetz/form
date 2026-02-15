<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Contract\FormInterface;
use JDZ\Form\Contract\FormFieldsetInterface;
use JDZ\Form\Contract\FormRowInterface;
use JDZ\Form\Exception\FormException;
use JDZ\Renderer\Element;

class Form extends Element implements FormInterface
{
    protected string $renderer = 'form';
    public string $prefix = '';
    public string $action = '';
    public string $method = FormInterface::METHOD_POST;
    public bool $multipart = false;
    public bool $vertical = true;
    public bool $wide = false;
    public array $errors = [];
    public array $fieldsets = [];
    public array $fieldrows = [];
    public array $buttons = [];
    public FormData $data;

    public bool $csrf = false;
    public bool $captcha = false;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function usesCaptcha(): bool
    {
        return $this->captcha;
    }

    public function withCaptcha(bool $captcha = true): static
    {
        $this->captcha = $captcha;
        return $this;
    }

    protected function onBeforeInit(FormData $data): void {}

    public function init(FormData $data): void
    {
        $this->onBeforeInit($data);

        $this->setData($data);

        $this->populate();
        $this->onAfterPopulate();
        $this->onFillValues();
        $this->onPrepare();

        $fieldsets = array_keys($this->fieldsets);
        if (false !== ($k = array_search('main', $fieldsets))) {
            unset($fieldsets[$k]);
        }
        if (false !== ($k = array_search('captcha', $fieldsets))) {
            unset($fieldsets[$k]);
        }
        $this->setFieldsetsOrder($fieldsets);
    }

    public function submit(): bool
    {
        $this->filter();
        $this->validate();
        return count($this->errors) === 0;
    }

    public function filter(): void
    {
        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->fields as $field) {
                $field->filter($this->data);
            }
        }

        foreach ($this->fieldrows as $field) {
            $field->filter($this->data);
        }
    }

    public function validate(): void
    {
        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->fields as $field) {
                if (false === $field->validate($this->data)) {
                    $this->errors = array_merge($this->errors, $field->errors);
                }
            }
        }

        foreach ($this->fieldrows as $field) {
            if (false === $field->validate($this->data)) {
                $this->errors = array_merge($this->errors, $field->errors);
            }
        }
    }

    public function setData(FormData $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function setPrefix(string $prefix): static
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function setMethod(string $method = FormInterface::METHOD_POST): static
    {
        $this->method = $method;
        return $this;
    }

    public function withCsrf(bool $csrf = true): static
    {
        $this->csrf = $csrf;
        return $this;
    }

    public function withMultipart(bool $multipart = true): static
    {
        $this->multipart = $multipart;
        return $this;
    }

    public function withVertical(bool $vertical = true): static
    {
        $this->vertical = $vertical;
        return $this;
    }

    public function withWide(bool $wide = true): static
    {
        $this->wide = $wide;
        return $this;
    }

    public function makeFormFieldset(string $name): FormFieldsetInterface
    {
        try {
            return $this->getFieldset($name);
        } catch (\Exception $e) {
        }

        $fieldset = new FormFieldset($name);
        return $fieldset;
    }

    public function makeFormRow(string $name, string $type = ''): FormRowInterface
    {
        try {
            return $this->getField($name);
        } catch (\Exception $e) {
        }

        return new FormRow($name);
    }

    public function getFieldset(string $name): FormFieldsetInterface
    {
        if (!isset($this->fieldsets[$name])) {
            throw new FormException('Fieldset ' . $this->getName() . '.' . $name . ' not found');
        }

        return $this->fieldsets[$name];
    }

    public function addFieldset(FormFieldsetInterface $fieldset): FormFieldsetInterface
    {
        $name = $fieldset->getName();
        if (!isset($this->fieldsets[$name])) {
            $this->fieldsets[$name] = $fieldset;
        }

        return $this->getFieldset($name);
    }

    public function hasFieldset(string $name): bool
    {
        return isset($this->fieldsets[$name]);
    }

    public function removeFieldset(string $name): static
    {
        if (isset($this->fieldsets[$name])) {
            unset($this->fieldsets[$name]);
        }

        return $this;
    }

    public function getField(string $name, string $fieldsetName = ''): FormRowInterface
    {
        if ('' !== $fieldsetName) {
            $fieldset = $this->getFieldset($fieldsetName);
            if ($fieldset->hasField($name)) {
                return $fieldset->getField($name);
            }
        } else {
            foreach ($this->fieldsets as $fieldset) {
                if ($fieldset->hasField($name)) {
                    return $fieldset->getField($name);
                }
            }
        }

        if (!isset($this->fieldrows[$name])) {
            throw new FormException('Field ' . $this->getName() . '.' . $name . ' not found');
        }

        return $this->fieldrows[$name];
    }

    public function addField(FormRowInterface $field, string $fieldsetName = ''): FormRowInterface
    {
        if ('' === $fieldsetName && 'form.hidden' !== $field->getRenderer()) {
            $fieldsetName = 'main';
        }

        if ('' !== $fieldsetName) {
            $fieldset = $this->getFieldset($fieldsetName);
            return $fieldset->addField($field);
        }

        $name = $field->getName();
        if (!isset($this->fieldrows[$name])) {
            $this->fieldrows[$name] = $field;
        }

        return $field;
    }

    public function hasField(string $name, string $fieldsetName = ''): bool
    {
        if ('' !== $fieldsetName) {
            if ($this->hasFieldset($fieldsetName)) {
                $fieldset = $this->getFieldset($fieldsetName);
                return $fieldset->hasField($name);
            }

            return false;
        }

        foreach ($this->fieldsets as $fieldset) {
            if (true === $fieldset->hasField($name)) {
                return true;
            }
        }

        return isset($this->fieldrows[$name]);
    }

    public function removeField(string $name, string $fieldsetName = ''): static
    {
        if ('' !== $fieldsetName) {
            $fieldset = $this->getFieldset($fieldsetName);
            $fieldset->removeField($name);
            return $this;
        }

        foreach ($this->fieldsets as $fieldset) {
            if ($fieldset->hasField($name)) {
                $fieldset->removeField($name);
                return $this;
            }
        }

        if (isset($this->fieldrows[$name])) {
            unset($this->fieldrows[$name]);
        }

        return $this;
    }

    public function getButton(string $name): FormButton
    {
        if (!isset($this->buttons[$name])) {
            throw new FormException('Button ' . $this->getName() . '.' . $name . ' not found');
        }

        return $this->buttons[$name];
    }

    public function addButton(FormButton $button): FormButton
    {
        $name = $button->getName();
        if (!isset($this->buttons[$name])) {
            $this->buttons[$name] = $button;
        }

        return $this->getButton($name);
    }

    public function hasButton(string $name): bool
    {
        return isset($this->buttons[$name]);
    }

    public function removeButton(string $name): static
    {
        if (isset($this->buttons[$name])) {
            unset($this->buttons[$name]);
        }

        return $this;
    }

    public function getValue(string $fieldName): mixed
    {
        return $this->getField($fieldName)->value;
    }

    public function getFieldNames(): array
    {
        $names = [];

        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->fields as $field) {
                $names[] = $field->getName();
            }
        }

        foreach ($this->fieldrows as $field) {
            $names[] = $field->getName();
        }

        return $names;
    }

    public function setFieldsetsOrder(array $names = []): static
    {
        array_unshift($names, 'main');
        $names[] = 'captcha';

        foreach (array_keys($this->fieldsets) as $nameCurrent) {
            $names[] = $nameCurrent;
        }

        $names = array_unique($names);

        $fieldsets = [];
        foreach ($names as $name) {
            if (isset($this->fieldsets[$name])) {
                $fieldsets[$name] = $this->getFieldset($name);
            }
        }

        $this->fieldsets = $fieldsets;
        return $this;
    }

    public function toData(): array
    {
        $data = parent::toData();

        $data['name'] = $this->getName();

        if ($this->fieldsets) {
            $data['fieldsets'] = $this->renderFieldsets();
        }

        if ($this->fieldrows) {
            $data['fields'] = $this->renderFields();
        }

        if ($this->buttons) {
            $data['buttons'] = $this->renderButtons();
        }

        return $data;
    }

    protected function populate(): void
    {
        $this->addFieldset($this->makeFormFieldset('main'));
    }

    protected function onAfterPopulate(): void {}

    protected function onFillValues(): void
    {
        foreach ($this->fieldsets as $fieldset) {
            foreach ($fieldset->fields as $field) {
                $field->onFillValues($this->data);
            }
        }

        foreach ($this->fieldrows as $field) {
            $field->onFillValues($this->data);
        }
    }

    protected function onPrepare(): void
    {
        foreach ($this->fieldsets as $fieldset) {
            if (false === $fieldset->onPrepare()) {
                $this->removeFieldset($fieldset->getName());
            }
        }

        foreach ($this->fieldrows as $field) {
            if (false === $field->onPrepare()) {
                $this->removeField($field->getName());
            }
        }
    }

    protected function renderAttrs(): array
    {
        if (true === $this->wide) {
            array_unshift($this->styles, 'wide');
        }

        if (true === $this->vertical) {
            array_unshift($this->styles, 'vertical');
        }

        $this->addDataAttr('id', $this->getName());

        $attrs = parent::renderAttrs();

        $attrs['action'] = $this->action;
        $attrs['method'] = $this->method;
        $attrs['name'] = $this->getName();

        if (true === $this->multipart) {
            $attrs['enctype'] = 'multipart/form-data';
        }

        return $attrs;
    }

    protected function renderFieldsets(): array
    {
        $fieldsets = [];
        $fieldsetsNotPositioned = [];
        foreach ($this->fieldsets as $fieldset) {
            if (0 === $fieldset->getPosition()) {
                $fieldsetsNotPositioned[] = $fieldset->toData();
                continue;
            }
            $fieldsets[$fieldset->getPosition()] = $fieldset->toData();
        }

        ksort($fieldsets);

        $fieldsets = array_merge(array_values($fieldsets), array_values($fieldsetsNotPositioned));

        $namedFieldsets = [];
        foreach ($this->fieldsets as $fieldset) {
            $namedFieldsets[$fieldset->getName()] = $fieldset->toData();
        }
        return $namedFieldsets;
    }

    protected function renderFields(): array
    {
        $fieldrows = [];
        foreach ($this->fieldrows as $field) {
            $fieldrows[$field->getName()] = $field->toData();
        }
        return $fieldrows;
    }

    protected function renderButtons(): array
    {
        $buttons = [];
        foreach ($this->buttons as $button) {
            $buttons[$button->getName()] = $button->toData();
        }
        return $buttons;
    }
}
