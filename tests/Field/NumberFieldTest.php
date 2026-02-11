<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Number;
use JDZ\Form\FormData;

class NumberFieldTest extends TestCase
{
    public function testTypeIsNumber(): void
    {
        $field = new Number('age');
        $this->assertSame('number', $field->type);
    }

    public function testSetValue(): void
    {
        $field = new Number('age');
        $field->init();
        $field->setValue('25');
        $this->assertSame(25, $field->value);
    }

    public function testUnsignedConvertsNegative(): void
    {
        $field = new Number('age');
        $field->init();
        $field->setValue('-5');
        $this->assertSame(5, $field->value);
    }

    public function testSignedAllowsNegative(): void
    {
        $field = new Number('temperature');
        $field->init();
        $field->withUnsigned(false);
        $field->setValue('-5');
        $this->assertSame(-5, $field->value);
    }

    public function testMinConstraint(): void
    {
        $field = new Number('quantity');
        $field->init();
        $field->setMin(1);
        $field->setValue('0');
        $this->assertSame(1, $field->value);
    }

    public function testMaxConstraint(): void
    {
        $field = new Number('quantity');
        $field->init();
        $field->setMax(100);
        $field->setValue('150');
        $this->assertSame(100, $field->value);
    }

    public function testFloatMode(): void
    {
        $field = new Number('price');
        $field->init();
        $field->withFloat();
        $field->setValue('3.14');
        $this->assertSame(3.14, $field->value);
    }

    public function testFilterSetsFieldValue(): void
    {
        $field = new Number('age');
        $field->init();
        $data = new FormData(['age' => '25']);

        $field->filter($data);

        $this->assertSame(25, $field->value);
    }

    public function testSetStep(): void
    {
        $field = new Number('quantity');
        $field->setStep(5);
        $this->assertNotNull($field);
    }
}
