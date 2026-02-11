<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter\StringFilter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class ArrayFilter extends StringFilter
{
    public string $name = 'array';

    protected function clean($value)
    {
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        $value = parent::clean($value);

        return $value;
    }
}
