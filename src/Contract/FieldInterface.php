<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

use JDZ\Form\FormData;

interface FieldInterface
{
    public function getName(): string;

    public function isEmpty(): bool;

    public function setValue($value): static;
    public function getValue(): mixed;

    public function getPosition(): int|string;

    public function onFillValues(FormData $data): void;

    public function validate(FormData $data): bool;

    public function setPrefix(string $prefix): static;
    
    public function filter(FormData $data): static;
    
    public function withArrayName(bool $withArrayName = true): static;
    public function withRequired(bool $required = true): static;
    public function withDisabled(bool $disabled = true): static;
    public function withReadonly(bool $readonly = true): static;

    public function addAriaAttr(string $key, mixed $value): static;

    public function toStatic(): string;
    public function toData(): array;
}
