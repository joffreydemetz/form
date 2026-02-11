<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class ColorFilter extends Filter
{
    // public string $type = 'color';

    public function clean($value)
    {
        $value = parent::clean($value);

        if ('' !== $value) {
            $value = strip_tags($value);
            $value = strtoupper($value);
            $value = trim($value, "# \t\n\r\0\x0B");
        }

        if (
            '' === $value // A color field can't be empty, we default to black. This is the same as the HTML5 spec.
            || !ctype_xdigit($value) // not hexa
            || (6 <> strlen($value) && 3 <> strlen($value)) // The value must be 6 or 3 characters long
        ) {
            $value = '000000';
        }

        return '#' . $value;
    }
}
