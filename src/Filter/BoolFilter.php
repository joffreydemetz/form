<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class BoolFilter extends Filter
{
    public string $name = 'bool';

    protected function clean($value): mixed
    {
        $value = parent::clean($value);

        return (bool) $value;
    }
}
