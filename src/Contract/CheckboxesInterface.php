<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

use JDZ\Form\Field\Checkbox;

interface CheckboxesInterface extends FormRowInterface
{
    public function getBoxes(): string;
    public function isVertical(): bool;
    public function getNbCols(): int;
    public function getMaxSelection(): int;
    public function getList(): array;
    public function getCheckedList(): array;
    public function isDisabledAtTheEnd(): bool;

    public function setNbCols(int $nbCols = 1): static;
    public function setList(array $list = []): static;
    public function setMaxSelection(int $maxSelection = 0): static;
    public function setRequiredErrorMessage(string $message): static;
    public function setBoxes(string $boxes): static;

    public function areDisabled(array $list = []): static;
    public function withDisabledAtTheEnd(bool $disabledAtTheEnd = true): static;
    public function withVertical(bool $vertical = true): static;

    public function addItem(Checkbox $item): static;
}
