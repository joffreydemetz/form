<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

interface BooleanInterface extends CheckboxesInterface
{
    public function setYesText(string $yesText): static;

    public function setNoText(string $noText): static;
}
