<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface FormRowInterface
{
    public function getName(): string;

    public function getRenderer(): string;

    public function setField(FieldInterface $field): static;

    public function setFieldset(FormFieldsetInterface $fieldset): static;

    public function setLabelText(string $labelText): static;

    public function setPrefix(string $prefix): static;

    public function setPosition(int $position): static;
}
