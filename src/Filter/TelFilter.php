<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class TelFilter extends Filter
{
    // public string $type = 'tel';

    public function clean($value)
    {
        $value = parent::clean($value);

        if ('' !== $value) {
            $value = preg_replace("/[^0-9\+\.\-\(\)]/", "", $value);
        }

        return $value;
    }
}
