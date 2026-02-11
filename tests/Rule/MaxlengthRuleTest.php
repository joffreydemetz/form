<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\MaxlengthRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class MaxlengthRuleTest extends TestCase
{
    public function testTruncatesValueExceedingMaxlength(): void
    {
        $rule = new MaxlengthRule();
        $field = new Text('test');
        $field->setMaxlength(5);
        $field->setValue('abcdefgh');
        $data = new FormData(['test' => 'abcdefgh']);

        $rule->execute($field, $data);

        $this->assertSame('abcde', $data->get('test'));
    }

    public function testDoesNotTruncateWithinLimit(): void
    {
        $rule = new MaxlengthRule();
        $field = new Text('test');
        $field->setMaxlength(10);
        $field->setValue('hello');
        $data = new FormData(['test' => 'hello']);

        $rule->execute($field, $data);

        $this->assertSame('hello', $data->get('test'));
    }

    public function testNameIsMaxlength(): void
    {
        $rule = new MaxlengthRule();
        $this->assertSame('maxlength', $rule->name);
    }
}
