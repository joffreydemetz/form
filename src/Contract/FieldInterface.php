<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface FieldInterface
{
    public function getName(): string;

    public function isEmpty(): bool;

    public function setValue($value);
    public function getValue();

    public function getPosition(): int|string;
}
