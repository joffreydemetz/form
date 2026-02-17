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

    public function getPrefix(): string;
    public function getAction(): string;
    public function getMethod(): string;
    public function isMultipart(): bool;
    public function isVertical(): bool;
    public function isWide(): bool;
    public function getErrors(): array;
    public function getData(): FormData;
    public function usesCsrf(): bool;

    public function usesCaptcha(): bool;
    public function withCaptcha(bool $captcha = true): static;

    public function setData(FormData $data): static;
    public function setAction(string $action): static;
    public function setMethod(string $method): static;

    public function withCsrf(bool $csrf = true): static;
    public function withMultipart(bool $multipart = true): static;
    public function withVertical(bool $vertical = true): static;
    public function withWide(bool $wide = true): static;

    public function makeFormFieldset(string $name): FormFieldsetInterface;
    public function getFieldset(string $name): ?FormFieldsetInterface;
    public function addFieldset(FormFieldsetInterface $fieldset): FormFieldsetInterface;
    public function hasFieldset(string $name): bool;
    public function removeFieldset(string $name): static;

    public function makeFormRow(string $name): FormRowInterface;
    public function getFormRow(string $name): ?FormRowInterface;
    public function addFormRow(FormRowInterface $field): FormRowInterface;
    public function hasFormRow(string $name): bool;
    public function removeFormRow(string $name): static;
}
