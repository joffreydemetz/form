<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Tel;
use JDZ\Form\FormData;

class TelFieldTest extends TestCase
{
    public function testTypeIsTel(): void
    {
        $field = new Tel('phone');
        $this->assertSame('tel', $field->type);
    }

    public function testDefaultMaxlength(): void
    {
        $field = new Tel('phone');
        $this->assertSame(15, $field->maxlength);
    }

    public function testInitAddsFilters(): void
    {
        $field = new Tel('phone');
        $field->init();
        $this->assertNotEmpty($field->filters);
    }

    public function testValidatesPhoneNumber(): void
    {
        $field = new Tel('phone');
        $field->setValue('0123456789');
        $data = new FormData(['phone' => '0123456789']);

        $result = $field->validate($data);
        $this->assertTrue($result);
    }
}
