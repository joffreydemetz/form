<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Url extends InputField
{
    public string $type = 'url';
    public string $placeholder = 'https://';
    public string $autocomplete = 'url';
    public string $errorMessage = 'Invalid url';

    public function init(): void
    {
        parent::init();

        $this->addFilter(
            new \JDZ\Form\Filter\UrlFilter()
        );
    }
}
