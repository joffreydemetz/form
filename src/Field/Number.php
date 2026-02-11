<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;
use JDZ\Form\FormData;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Number extends InputField
{
    public string $type = 'number';
    public string $errorMessage = 'Invalid number';
    public bool $unsigned = true;
    public bool $float = false;
    public ?int $min = null;
    public ?int $max = null;
    public int|string|null $step = null;
    protected bool $decimal = false;
    protected int $decimals = 0;

    public function init()
    {
        parent::init();

        if (true === $this->decimal) {
            $this->float = true;

            $this->addFilter(
                new \JDZ\Form\Filter\DecimalFilter([
                    'float' => $this->float,
                    'decimals' => $this->decimals,
                ])
            );
        } else {
            $this->addFilter(
                new \JDZ\Form\Filter\IntFilter()
            );
        }
    }

    public function withUnsigned(bool $unsigned = true)
    {
        $this->unsigned = $unsigned;

        if ($unsigned && null !== $this->min && $this->min < 0) {
            $this->min = 0;
        }

        if ($filter = $this->getFilter('int')) {
            $filter->config->set('unsigned', $this->unsigned);
        }

        return $this;
    }

    public function withFloat(bool $float = true)
    {
        $this->float = $float;

        if ($filter = $this->getFilter('int')) {
            $filter->config->set('float', $this->float);
        }

        return $this;
    }

    public function setMin(?int $min)
    {
        $this->min = $min;
        return $this;
    }

    public function setMax(?int $max)
    {
        $this->max = $max;
        return $this;
    }

    public function setStep(int|string|null $step)
    {
        $this->step = $step;
        return $this;
    }

    public function setValue($value)
    {
        if (true === $this->float) {
            $value = (float)$value;
        } else {
            $value = (int)$value;
        }

        if (true === $this->unsigned) {
            $value = abs($value);
        }

        if (null !== $this->min && $value < $this->min) {
            $value = $this->min;
        }

        if (null !== $this->max && $value > $this->max) {
            $value = $this->max;
        }

        return parent::setValue($value);
    }

    public function validate(FormData $data)
    {
        if (null !== $this->min && $data->get($this->name) < $this->min) {
            $data->set($this->name, $this->min);
        }

        if (null !== $this->max && $data->get($this->name) > $this->max) {
            $data->set($this->name, $this->max);
        }

        return parent::validate($data);
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        if (null !== $this->min) {
            $attrs['min'] = $this->min;
        }

        if (null !== $this->max) {
            $attrs['max'] = $this->max;
        }

        if (null !== $this->step) {
            $attrs['step'] = $this->step;
        }

        return $attrs;
    }
}
