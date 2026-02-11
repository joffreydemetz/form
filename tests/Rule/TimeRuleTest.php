<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\TimeRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class TimeRuleTest extends TestCase
{
    private TimeRule $rule;

    protected function setUp(): void
    {
        $this->rule = new TimeRule();
    }

    public function testPassesForValidTime(): void
    {
        $field = new Text('time');
        $field->setValue('14:30');
        $data = new FormData(['time' => '14:30']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testPassesForTimeWithSeconds(): void
    {
        $field = new Text('time');
        $field->setValue('14:30:00');
        $data = new FormData(['time' => '14:30:00']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsForInvalidTime(): void
    {
        $this->expectException(InvalidException::class);

        $field = new Text('time');
        $field->setValue('abc');
        $data = new FormData(['time' => 'abc']);

        $this->rule->execute($field, $data);
    }

    public function testSkipsEmptyValue(): void
    {
        $field = new Text('time');
        $field->setValue('');
        $data = new FormData(['time' => '']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }
}
