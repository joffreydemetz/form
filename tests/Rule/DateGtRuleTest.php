<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\DateGtRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class DateGtRuleTest extends TestCase
{
    public function testPassesWhenDateIsGreater(): void
    {
        $rule = new DateGtRule();
        $rule->setCompareTo('start_date');

        $field = new Text('end_date');
        $field->setValue('2024-12-31');
        $data = new FormData([
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);

        $rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsWhenDateIsNotGreater(): void
    {
        $this->expectException(InvalidException::class);

        $rule = new DateGtRule();
        $rule->setCompareTo('start_date');

        $field = new Text('end_date');
        $field->setValue('2023-01-01');
        $data = new FormData([
            'start_date' => '2024-01-01',
            'end_date' => '2023-01-01',
        ]);

        $rule->execute($field, $data);
    }
}
