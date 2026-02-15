<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class IntFilter extends Filter
{
    // public string $name = 'int';

    public function __construct(array $config = [])
    {
        $config = array_merge([
            'unsigned' => true,
            'float' => false,
        ], $config);

        parent::__construct($config);
    }

    public function withUnsigned(bool $unsigned = true): static
    {
        $this->config->set('unsigned', $unsigned);
        return $this;
    }

    public function withFloat(bool $float = true): static
    {
        $this->config->set('float', $float);
        return $this;
    }

    protected function clean($value): mixed
    {
        $value = parent::clean($value);

        if ($this->config->getBool('float')) {
            $value = (float) $value;
        } else {
            $value = (int) $value;
        }

        if ($this->config->getBool('unsigned')) {
            $value = abs($value);
        }

        return $value;
    }
}
