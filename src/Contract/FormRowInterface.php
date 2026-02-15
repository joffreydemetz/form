<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

use JDZ\Form\FormData;

interface FormRowInterface
{
    public function getName(): string;

    public function getRenderer(): string;

    public function setField(FieldInterface $field): static;

    public function setFieldset(FormFieldsetInterface $fieldset): static;

    public function setLabelText(string $labelText): static;
    public function withLabel(bool $label = true): static;

    public function setPrefix(string $prefix): static;

    public function setPosition(int $position): static;
    public function getPosition(): int;

    public function onFillValues(FormData $data): void;
}
