<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

use JDZ\Form\FormButton;
use JDZ\Renderer\Span;

interface InputgroupFormRowInterface
{
    public function addPart(Span|FormButton|FieldInterface $part, ?string $uid = null): static;
    public function getPart(string $uid): Span|FormButton|FieldInterface;
    public function hasPart(string $uid): bool;
}
