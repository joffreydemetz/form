<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Search extends InputField
{
    public string $type = 'search';
    public string $autocomplete = 'search';
}
