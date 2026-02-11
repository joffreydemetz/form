<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use Carbon\Carbon;
use JDZ\Form\Filter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class DateFilter extends Filter
{
    // public string $type = 'date';

    public function clean($value)
    {
        if (!$value) {
            return '';
        }

        $value = preg_replace("/^([0-9]{4}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2})$/", "$1 $2:00", $value);

        if (preg_match("/^(0000-00-00|1000-01-01).*$/", $value)) {
            return '';
        }

        $test = Carbon::createFromFormat($this->config->get('format'), $value);

        if (false === $test) {
            return '';
        }

        return $test->format($this->config->get('format'));
    }
}
