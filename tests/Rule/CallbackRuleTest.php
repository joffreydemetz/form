<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\CallbackRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class CallbackRuleTest extends TestCase
{
    public function testCallbackIsInvoked(): void
    {
        $called = false;
        $rule = new CallbackRule();
        $rule->setCallback(function ($field, $data, $message) use (&$called) {
            $called = true;
        });

        $field = new Text('test');
        $field->setValue('hello');
        $data = new FormData(['test' => 'hello']);

        $rule->execute($field, $data);

        $this->assertTrue($called);
    }

    public function testNameIsCondition(): void
    {
        $rule = new CallbackRule();
        $this->assertSame('condition', $rule->name);
    }

    public function testCustomMessage(): void
    {
        $rule = new CallbackRule('Custom validation failed');
        $this->assertSame('Custom validation failed', $rule->message);
    }
}
