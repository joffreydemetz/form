<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\DateRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class DateRuleTest extends TestCase
{
    private DateRule $rule;

    protected function setUp(): void
    {
        $this->rule = new DateRule();
    }

    public function testPassesForValidDate(): void
    {
        $field = new Text('date');
        $field->setValue('2024-06-15');
        $data = new FormData(['date' => '2024-06-15']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testPassesForDatetime(): void
    {
        $field = new Text('date');
        $field->setValue('2024-06-15 14:30');
        $data = new FormData(['date' => '2024-06-15 14:30']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsForInvalidDate(): void
    {
        $this->expectException(InvalidException::class);

        $field = new Text('date');
        $field->setValue('not-a-date');
        $data = new FormData(['date' => 'not-a-date']);

        $this->rule->execute($field, $data);
    }

    public function testSkipsEmptyValue(): void
    {
        $field = new Text('date');
        $field->setValue('');
        $data = new FormData(['date' => '']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }
}
