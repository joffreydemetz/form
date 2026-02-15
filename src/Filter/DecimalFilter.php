<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter\IntFilter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class DecimalFilter extends IntFilter
{
    // public string $name = 'decimal';

    public function __construct(array $config = [])
    {
        $config = array_merge([
            'decimals' => 1, // Decimal count
            'float' => true,
        ], $config);

        parent::__construct($config);
    }

    protected function clean($value): mixed
    {
        $value = parent::clean($value);

        $value = number_format($value, $this->config->getInt('decimals'), '.', '');

        return $value;
    }
}
