<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Text extends InputField
{
    public int $maxlength = 250;
}
