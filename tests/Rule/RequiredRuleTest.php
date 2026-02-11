<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\RequiredRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\RequiredException;

class RequiredRuleTest extends TestCase
{
    public function testNameIsRequired(): void
    {
        $rule = new RequiredRule();
        $this->assertSame('required', $rule->name);
    }

    public function testPassesWhenFieldHasValue(): void
    {
        $rule = new RequiredRule();
        $field = new Text('test');
        $field->withRequired();
        $field->setValue('hello');
        $data = new FormData(['test' => 'hello']);

        $rule->execute($field, $data);
        $this->assertTrue(true); // No exception thrown
    }

    public function testThrowsWhenFieldIsEmpty(): void
    {
        $this->expectException(RequiredException::class);

        $rule = new RequiredRule();
        $field = new Text('test');
        $field->withRequired();
        $field->setValue('');
        $data = new FormData(['test' => '']);

        $rule->execute($field, $data);
    }

    public function testCustomMessage(): void
    {
        $rule = new RequiredRule('Name is required');
        $this->assertSame('Name is required', $rule->message);
    }

    public function testDefaultMessage(): void
    {
        $rule = new RequiredRule();
        $this->assertSame('Field is required', $rule->message);
    }
}
