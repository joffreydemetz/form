<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface FormFieldsetInterface
{
    public function getName(): string;
    public function getUid(): string;
    public function getLabel(): string;
    public function getDescription(): string;

    public function setUid(string $uid): static;
    public function setLabel(string $label): static;
    public function setDescription(string $description): static;

    public function getFormRows(): array;
    public function getFormRow(string $fieldName): ?FormRowInterface;
    public function addFormRow(FormRowInterface $field): FormRowInterface;
    public function hasFormRow(string $key): bool;
    public function removeFormRow(string $key): static;
    public function setFormRowPosition(string $fieldName, int|string $position, string $direction = 'before'): static;
}
