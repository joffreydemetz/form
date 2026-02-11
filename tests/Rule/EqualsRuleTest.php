<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\EqualsRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class EqualsRuleTest extends TestCase
{
    public function testPassesWhenValuesMatch(): void
    {
        $rule = new EqualsRule();
        $rule->setCompareTo('password');

        $field = new Text('password_confirm');
        $field->setValue('secret123');
        $data = new FormData([
            'password' => 'secret123',
            'password_confirm' => 'secret123',
        ]);

        $rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsWhenValuesDontMatch(): void
    {
        $this->expectException(InvalidException::class);

        $rule = new EqualsRule();
        $rule->setCompareTo('password');

        $field = new Text('password_confirm');
        $field->setValue('different');
        $data = new FormData([
            'password' => 'secret123',
            'password_confirm' => 'different',
        ]);

        $rule->execute($field, $data);
    }

    public function testNameIsEquals(): void
    {
        $rule = new EqualsRule();
        $this->assertSame('equals', $rule->name);
    }
}
