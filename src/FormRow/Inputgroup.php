<?php

declare(strict_types=1);

namespace JDZ\Form\FormRow;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Contract\InputgroupFormRowInterface;
use JDZ\Form\FormButton;
use JDZ\Form\FormData;
use JDZ\Form\FormRow;
use JDZ\Renderer\Span;

class Inputgroup extends FormRow implements InputgroupFormRowInterface
{
    protected string $renderer = 'inputgroup';
    public array $parts = [];

    public function addPart(Span|FormButton|FieldInterface $part, ?string $uid = null): static
    {
        if (!$uid) {
            if (method_exists($part, 'getName')) {
                $uid = $part->getName();
            } else {
                $uid = \is_object($part) ? \spl_object_hash($part) : $part;
            }
        }
        $this->parts[$uid] = $part;
        return $this;
    }

    public function getPart(string $uid): Span|FormButton|FieldInterface
    {
        if (!isset($this->parts[$uid])) {
            throw new \RuntimeException('Part not found');
        }
        return $this->parts[$uid];
    }

    public function hasPart(string $uid): bool
    {
        return isset($this->parts[$uid]);
    }

    public function onFillValues(FormData $data): void
    {
        foreach ($this->parts as $part) {
            if ($part instanceof FieldInterface) {
                $value = $data->get($part->getName());
                $part->setValue($value);
            }
        }
    }

    public function toData(): array
    {
        $this->addStyle('field-group');

        $data = parent::toData();

        $data['parts'] = $this->getParts();

        return $data;
    }

    public function getParts(): array
    {
        $parts = [];
        foreach ($this->parts as $part) {
            if ($part instanceof FieldInterface) {
                if ('' !== $this->prefix) {
                    $part->setPrefix($this->prefix);
                }
                if (true === $this->required) {
                    $part->withRequired();
                }
            }
            $parts[] = $part->toData();
        }

        return $parts;
    }
}
