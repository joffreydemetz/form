<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface BooleanInterface extends CheckboxesInterface
{
    public function setTrueText(string $trueText): static;

    public function setFalseText(string $falseText): static;
}
