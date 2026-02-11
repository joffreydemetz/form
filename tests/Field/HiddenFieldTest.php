<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Hidden;
use JDZ\Form\FormData;

class HiddenFieldTest extends TestCase
{
    public function testTypeIsHidden(): void
    {
        $field = new Hidden('id');
        $this->assertSame('hidden', $field->type);
    }

    public function testSetValue(): void
    {
        $field = new Hidden('id');
        $field->setValue('42');
        $this->assertSame('42', $field->value);
    }

    public function testToDataIncludesHiddenFlag(): void
    {
        $field = new Hidden('id');
        $field->setValue('42');
        $data = $field->toData();
        $this->assertTrue($data['hidden']);
    }

    public function testToDataClearsPresentation(): void
    {
        $field = new Hidden('id');
        $data = $field->toData();
        $this->assertSame('', $data['attrs']['placeholder'] ?? '');
    }
}
