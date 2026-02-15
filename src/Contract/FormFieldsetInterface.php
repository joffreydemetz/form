<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface FormFieldsetInterface
{
    public function getName(): string;

    public function getField(string $fieldName): ?FormRowInterface;
    public function addField(FormRowInterface $field): FormRowInterface;
    public function hasField(string $key): bool;

    public function setFieldPosition(string $fieldName, int|string $position, string $direction = 'before');
}
