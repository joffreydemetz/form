<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface FormRowInterface
{
    public function getRenderer(): string;

    public function setField(FieldInterface $field);

    public function setFieldset(FormFieldsetInterface $fieldset);

    public function setLabelText(string $labelText);

    public function setPrefix(string $prefix);
}
