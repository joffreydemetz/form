<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\DateLtRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class DateLtRuleTest extends TestCase
{
    public function testPassesWhenDateIsLess(): void
    {
        $rule = new DateLtRule();
        $rule->setCompareTo('end_date');

        $field = new Text('start_date');
        $field->setValue('2024-01-01');
        $data = new FormData([
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);

        $rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsWhenDateIsNotLess(): void
    {
        $this->expectException(InvalidException::class);

        $rule = new DateLtRule();
        $rule->setCompareTo('end_date');

        $field = new Text('start_date');
        $field->setValue('2025-01-01');
        $data = new FormData([
            'start_date' => '2025-01-01',
            'end_date' => '2024-12-31',
        ]);

        $rule->execute($field, $data);
    }
}
