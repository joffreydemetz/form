<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface FormFieldsetInterface
{
    public function getField(string $fieldName): ?FormRowInterface;

    public function setFieldPosition(string $fieldName, int|string $position, string $direction = 'before');
}
