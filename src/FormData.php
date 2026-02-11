<?php
declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Utils\Data;

class FormData extends Data
{
    public function __construct(array $properties = [])
    {
        if ($properties) {
            $this->sets($properties);
        }
    }

    public function getProperties(bool $object = true)
    {
        $data = $this->all();
        if (true === $object) {
            $data = (object)$data;
        }
        return $data;
    }

    public function export(): array
    {
        return $this->all();
    }
}
