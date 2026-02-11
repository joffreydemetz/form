<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Date;
use JDZ\Form\FormData;

class DateFieldTest extends TestCase
{
    public function testTypeIsDate(): void
    {
        $field = new Date('birthdate');
        $this->assertSame('date', $field->type);
    }

    public function testInitAddsFilters(): void
    {
        $field = new Date('birthdate');
        $field->init();
        $this->assertNotEmpty($field->filters);
    }

    public function testSetValueSanitizesDate(): void
    {
        $field = new Date('birthdate');
        $field->init();
        $field->setValue('2024-06-15');
        $this->assertSame('2024-06-15', $field->value);
    }

    public function testSetMinDate(): void
    {
        $field = new Date('birthdate');
        $field->init();
        $field->setMin('2020-01-01');
        $this->assertSame('2020-01-01', $field->min);
    }

    public function testSetMaxDate(): void
    {
        $field = new Date('birthdate');
        $field->init();
        $field->setMax('2030-12-31');
        $this->assertSame('2030-12-31', $field->max);
    }

    public function testValidatesDateFormat(): void
    {
        $field = new Date('birthdate');
        $field->init();
        $field->setValue('2024-06-15');
        $data = new FormData(['birthdate' => '2024-06-15']);

        $result = $field->validate($data);
        $this->assertTrue($result);
    }

    public function testToStaticReturnsReadableDate(): void
    {
        $field = new Date('birthdate');
        $field->init();
        $field->setValue('2024-06-15');

        $static = $field->toStatic();
        $this->assertNotEmpty($static);
    }
}
