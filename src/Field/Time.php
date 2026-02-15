<?php

declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Time extends InputField
{
    public string $type = 'time';
    public string $pattern = '[0-9]{2}:[0-9]{2}';
    public string $min = '';
    public string $max = '';

    public function init(): void
    {
        parent::init();

        $this->addFilter(
            new \JDZ\Form\Filter\TimeFilter()
        );
    }

    public function toStatic(): string
    {
        return $this->readableValue();
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        $attrs['value'] = $this->readableValue();

        if ('' !== $this->min) {
            $attrs['min'] = $this->cleanValue($this->min);
        }

        if ('' !== $this->max) {
            $attrs['max'] = $this->cleanValue($this->max);
        }

        return $attrs;
    }

    protected function readableValue(): string
    {
        return $this->cleanValue((string)$this->value);
    }

    protected function cleanValue(string $value): string
    {
        $hours = '00';
        $minutes = '00';

        if ($value) {
            if (preg_match("/^([0-9]{2}):([0-9]{2})(:[0-9]{2})?$/", $value, $m)) {
                if (intval($m[1]) <= 23) {
                    $hours = $m[1];
                }
                if (intval($m[2]) <= 59) {
                    $minutes = $m[2];
                }
            }
        }

        return $hours . ':' . $minutes;
    }
}
