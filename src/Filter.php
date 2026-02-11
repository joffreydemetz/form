<?php

declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Utils\Data;

class Filter
{
    public string $name = 'raw';
    public Data $config;

    public function __construct(array $config = [])
    {
        $this->config = new Data();
        if ($config) {
            $this->config->sets($config);
        }
    }

    public function execute(FieldInterface $field, FormData $data): void
    {
        $value = $data->get($field->getName());

        if (is_array($value)) {
            foreach ($value as $k => &$v) {
                $v = $this->clean($v);
            }
        } else {
            $value = $this->clean($value);
        }

        $field->setValue($value);
        $data->set($field->getName(), $value);
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    protected function clean($value)
    {
        if ($value) {
            if (is_array($value)) {
                foreach ($value as &$v) {
                    $v = $this->clean($v);
                }
            } else {
                $value = trim($value);
            }
        }
        return $value;
    }
}
