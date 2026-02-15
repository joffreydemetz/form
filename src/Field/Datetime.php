<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\Field\Date;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Datetime extends Date
{
    public string $type = 'datetime-local';
    public string $pattern = '[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}';

    protected string $formatInput = 'Y-m-d H:i:s';
    protected string $formatOutput = 'Y-m-d\TH:i';
    protected string $formatReadable = 'd/m/Y H:i';

    protected function sanitizeInputValueDate(?string $value): ?string
    {
        if ($value) {
            $value = preg_replace("/^(\d{4}-\d{2}-\d{2})T(\d{2}):(\d{2}):(\d{2})$/", "$1 $2:$3:$4", $value);
            $value = preg_replace("/^(\d{4}-\d{2}-\d{2})T(\d{2}):(\d{2})(:(\d{2}))?$/", "$1 $2:$3:00", $value);
        }

        return parent::sanitizeInputValueDate($value);
    }
}
