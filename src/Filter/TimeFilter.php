<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class TimeFilter extends Filter
{
    // public string $type = 'time';

    public function clean($value)
    {
        $hours = '00';
        $minutes = '00';

        if ($value = parent::clean($value)) {
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
