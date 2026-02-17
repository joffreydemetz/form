<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface BooleanInterface extends CheckboxesInterface
{
    public function setTrueValue(string $trueValue): static;

    public function setFalseValue(string $falseValue): static;
}
