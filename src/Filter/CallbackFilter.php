<?php

declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Filter\StringFilter;
use JDZ\Form\FormData;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class CallbackFilter extends StringFilter
{
    // public string $name = 'callback';

    public function execute(FieldInterface $field, FormData $data): void
    {
        $callback = $this->config->get('callback');
        $callback($field, $data);
    }

    public function clean($value): mixed
    {
        $value = parent::clean($value);

        $callback = $this->config->get('callback');
        return $callback($value);
    }
}
