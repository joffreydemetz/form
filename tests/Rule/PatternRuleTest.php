<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\PatternRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class PatternRuleTest extends TestCase
{
    public function testPassesWhenPatternMatches(): void
    {
        $rule = new PatternRule();
        $rule->setPattern('^[0-9]+$');
        $field = new Text('test');
        $field->setValue('12345');
        $data = new FormData(['test' => '12345']);

        $rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsWhenPatternDoesNotMatch(): void
    {
        $this->expectException(InvalidException::class);

        $rule = new PatternRule();
        $rule->setPattern('^[0-9]+$');
        $field = new Text('test');
        $field->setValue('abc');
        $data = new FormData(['test' => 'abc']);

        $rule->execute($field, $data);
    }

    public function testSkipsEmptyValue(): void
    {
        $rule = new PatternRule();
        $rule->setPattern('^[0-9]+$');
        $field = new Text('test');
        $field->setValue('');
        $data = new FormData(['test' => '']);

        $rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testCustomMessage(): void
    {
        $rule = new PatternRule('Must be numeric');
        $rule->setPattern('^[0-9]+$');
        $this->assertSame('Must be numeric', $rule->message);
    }
}
