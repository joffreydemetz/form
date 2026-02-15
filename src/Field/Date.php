<?php

declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\FormData;
use JDZ\Form\InputField;
use Carbon\Carbon;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 * @todo check if max > min
 */
class Date extends InputField
{
    public string $type = 'date';
    public string $pattern = '[0-9]{4}-[0-9]{2}-[0-9]{2}';
    public string $min = '';
    public string $max = '';
    protected string $tz = 'UTC';
    protected string $formatInput = 'Y-m-d';
    protected string $formatOutput = 'Y-m-d';
    protected string $formatReadable = 'd/m/Y';

    public function __construct(string $name, array $config = [])
    {
        if (!empty($config['tz'])) {
            $this->setTimezone($config['tz']);
        }

        parent::__construct($name, $config);
    }

    public function init(): void
    {
        parent::init();

        $this->addFilter(
            new \JDZ\Form\Filter\DateFilter([
                'format' => $this->formatInput,
            ])
        );
    }

    public function setTimezone(string $tz): static
    {
        $this->tz = $tz;
        return $this;
    }

    public function setMin(string $min): static
    {
        $this->min = $this->sanitizeInputValueDate($min);
        return $this;
    }

    public function setMax(string $max): static
    {
        $this->max = $this->sanitizeInputValueDate($max);
        return $this;
    }

    public function setValue(mixed $value): static
    {
        return parent::setValue($this->sanitizeInputValueDate($value));
    }

    public function validate(FormData $data): bool
    {
        if ('time' === $this->type) {
            if (!isset($this->rules['time'])) {
                $this->addRule(new \JDZ\Form\Rule\TimeRule($this->errorMessage));
            }
        } else {
            if (!isset($this->rules['date'])) {
                $rule = new \JDZ\Form\Rule\DateRule($this->errorMessage);
                $rule->setPattern(str_replace('T', '\s+', $this->pattern));
                $this->addRule($rule);
            }
        }

        return parent::validate($data);
    }

    public function toStatic(): string
    {
        return $this->readableValue();
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        $attrs['value'] = $this->sanitizeOutputValueDate($this->value);

        if ('' !== $this->min) {
            $attrs['min'] = $this->sanitizeOutputValueDate($this->min);
        }

        if ('' !== $this->max) {
            $attrs['max'] = $this->sanitizeOutputValueDate($this->max);
        }

        return $attrs;
    }

    protected function sanitizeInputValueDate(?string $value): ?string
    {
        if ($value) {
            if (preg_match("/^(0000-00-00|1000-01-01).*$/", $value)) {
                $value = '';
            } elseif (false === ($test = Carbon::createFromFormat($this->formatInput, $value))) {
                $value = '';
            } else {
                $value = $test->format($this->formatInput);
            }
        } else {
            $value = '';
        }

        return $value;
    }

    protected function sanitizeOutputValueDate(?string $value): ?string
    {
        if ($value) {
            if (preg_match("/^(0000-00-00|1000-01-01).*$/", $value)) {
                $value = '';
            } else {
                $date = Carbon::createFromFormat($this->formatInput, $value, $this->tz);
                $value = $date->format($this->formatOutput);
            }
        }

        return $value;
    }

    protected function readableValue(): string
    {
        if ($this->value && !preg_match("/^(0000-00-00|1000-01-01).*$/", $this->value)) {
            $date = Carbon::createFromFormat($this->formatInput, $this->value, $this->tz);
            $value = $date->format($this->formatReadable);
        } else {
            $value = '';
        }

        return $value;
    }
}
