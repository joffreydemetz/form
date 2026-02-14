<?php

declare(strict_types=1);

namespace JDZ\Form\Rule;

use JDZ\Form\FormError;
use JDZ\Form\Rule;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class CompareRule extends Rule
{
    public string $name = 'compare';
    public string $message = 'Compare failed';
    public ?FormError $errorCode = FormError::COMPARE_FAILED;
    public $compareTo;

    public function setCompareTo(string $compareTo)
    {
        $this->compareTo = $compareTo;
        return $this;
    }
}
