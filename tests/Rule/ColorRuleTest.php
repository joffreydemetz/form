<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\ColorRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class ColorRuleTest extends TestCase
{
    private ColorRule $rule;

    protected function setUp(): void
    {
        $this->rule = new ColorRule();
    }

    public function testPassesForValidHexColor(): void
    {
        $field = new Text('color');
        $field->setValue('#FF0000');
        $data = new FormData(['color' => '#FF0000']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsForInvalidColor(): void
    {
        $this->expectException(InvalidException::class);

        $field = new Text('color');
        $field->setValue('notacolor');
        $data = new FormData(['color' => 'notacolor']);

        $this->rule->execute($field, $data);
    }

    public function testSkipsEmptyValue(): void
    {
        $field = new Text('color');
        $field->setValue('');
        $data = new FormData(['color' => '']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testNameIsColor(): void
    {
        $this->assertSame('color', $this->rule->name);
    }
}
