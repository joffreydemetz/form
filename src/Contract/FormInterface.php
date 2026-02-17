<?php

declare(strict_types=1);

namespace JDZ\Form\Contract;

use JDZ\Form\FormData;
use JDZ\Form\Contract\FormFieldsetInterface;

interface FormInterface
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    public function init(FormData $data): void;
    public function submit(): bool;
    public function filter(): void;
    public function validate(): void;

    public function getName(): string;

    public function usesCaptcha(): bool;
    public function withCaptcha(bool $captcha = true): static;

    public function setData(FormData $data): static;

    public function setAction(string $action): static;

    public function setMethod(string $method): static;

    public function withCsrf(bool $csrf = true): static;
    public function withMultipart(bool $multipart = true): static;
    public function withVertical(bool $vertical = true): static;
    public function withWide(): static;

    public function makeFormFieldset(string $name): FormFieldsetInterface;
    public function getFormFieldset(string $name): ?FormFieldsetInterface;
    public function addFormFieldset(FormFieldsetInterface $fieldset): FormFieldsetInterface;
    public function hasFormFieldset(string $name): bool;
    public function removeFormFieldset(string $name): static;
    public function setFormFieldsetPosition(string $fieldsetName, int|string $position, string $direction = 'before'): static;

    public function makeFormRow(string $name): FormRowInterface;
    public function getFormRow(string $name): ?FormRowInterface;
    public function addFormRow(FormRowInterface $field): FormRowInterface;
    public function hasFormRow(string $name): bool;
    public function removeFormRow(string $name): static;
    public function setFormRowPosition(string $fieldName, int|string $position, string $direction = 'before'): static;
}
