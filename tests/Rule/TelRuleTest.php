<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\TelRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class TelRuleTest extends TestCase
{
    private TelRule $rule;

    protected function setUp(): void
    {
        $this->rule = new TelRule();
    }

    public function testPassesForValidPhoneNumber(): void
    {
        $field = new Text('phone');
        $field->setValue('+33123456789');
        $data = new FormData(['phone' => '+33123456789']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testPassesForPhoneWithDots(): void
    {
        $field = new Text('phone');
        $field->setValue('01.23.45.67.89');
        $data = new FormData(['phone' => '01.23.45.67.89']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsForTooShortNumber(): void
    {
        $this->expectException(InvalidException::class);

        $field = new Text('phone');
        $field->setValue('123');
        $data = new FormData(['phone' => '123']);

        $this->rule->execute($field, $data);
    }

    public function testSkipsEmptyValue(): void
    {
        $field = new Text('phone');
        $field->setValue('');
        $data = new FormData(['phone' => '']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }
}
