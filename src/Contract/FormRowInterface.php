<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

use JDZ\Form\FormData;

interface FormRowInterface
{
    public function getName(): string;
    public function getRenderer(): string;
    public function getUid(): string;
    public function getPrefix(): string;
    public function getLabelText(): string;
    public function getTip(): string;
    public function hasLabel(): bool;
    public function isRequired(): bool;
    public function isDisabled(): bool;
    public function isReadonly(): bool;
    public function isStatic(): bool;
    public function isLabelOnTop(): bool;
    public function hasArrayName(): bool;
    public function hasOffset(): bool;
    public function getErrors(): array;
    public function getFieldset(): ?FormFieldsetInterface;
    public function getField(): ?FieldInterface;

    public function setField(FieldInterface $field): static;
    public function setFieldset(FormFieldsetInterface $fieldset): static;
    public function setLabelText(string $labelText): static;
    public function setPrefix(string $prefix): static;
    public function setTip(string $tip): static;
    public function setPosition(int $position): static;
    public function getPosition(): int;
    public function setFormRowPosition(int|string $position, string $direction = 'before'): static;
    public function setFormRowPositionAfter(string $offsetFieldName): static;
    public function setFormRowPositionBefore(string $offsetFieldName): static;

    public function withLabel(bool $label = true): static;
    public function withRequired(bool $required = true): static;
    public function withDisabled(bool $disabled = true): static;
    public function withReadonly(bool $readonly = true): static;
    public function withLabelOnTop(bool $labelOnTop = true): static;
    public function withArrayName(bool $arrayName = true): static;
    public function withOffset(bool $offset = true): static;

    public function onPrepare(): bool;
    public function onFillValues(FormData $data): void;
    public function filter(FormData $data): static;
    public function validate(FormData $data): bool;
}
