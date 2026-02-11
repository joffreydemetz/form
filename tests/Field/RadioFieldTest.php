<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Radio;
use JDZ\Form\FormData;

class RadioFieldTest extends TestCase
{
    public function testTypeIsRadio(): void
    {
        $field = new Radio('gender');
        $this->assertSame('radio', $field->type);
    }

    public function testRendererIsRadio(): void
    {
        $field = new Radio('gender');
        $data = $field->toData();
        $this->assertSame('radio', $data['renderer']);
    }
}
