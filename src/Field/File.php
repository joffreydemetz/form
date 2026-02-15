<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 * @todo rules for filesize, filetype, mime, etc.
 */
class File extends InputField
{
    public string $type = 'file';
    protected string $accept = '';
    protected bool $multiple = false;

    public function withMultiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function setAccept(string $accept): static
    {
        $this->accept = $accept;
        return $this;
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        if (true === $this->multiple) {
            $attrs['multiple'] = 'multiple';
        }

        if ('' !== $this->accept) {
            $attrs['accept'] = $this->accept;
        }

        return $attrs;
    }
}
