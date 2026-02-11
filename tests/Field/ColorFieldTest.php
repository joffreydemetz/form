<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Color;
use JDZ\Form\FormData;

class ColorFieldTest extends TestCase
{
    public function testTypeIsColor(): void
    {
        $field = new Color('color');
        $this->assertSame('color', $field->type);
    }

    public function testInitAddsFilters(): void
    {
        $field = new Color('color');
        $field->init();
        $this->assertNotEmpty($field->filters);
    }

    public function testSanitizesValidColorValue(): void
    {
        $field = new Color('color');
        $field->init();
        $field->setValue('#FF0000');
        $this->assertSame('#FF0000', $field->value);
    }

    public function testSanitizesInvalidColorToEmpty(): void
    {
        $field = new Color('color');
        $field->init();
        $field->setValue('notacolor');
        $this->assertSame('', $field->value);
    }

    public function testValidatesHexColor(): void
    {
        $field = new Color('color');
        $field->init();
        $field->setValue('#FF0000');
        $data = new FormData(['color' => '#FF0000']);

        $result = $field->validate($data);
        $this->assertTrue($result);
    }

    public function testFilterSanitizesColor(): void
    {
        $field = new Color('color');
        // Use ColorFilter directly to test sanitization
        $filter = new \JDZ\Form\Filter\ColorFilter();
        $data = new FormData(['color' => '#ff0000']);

        $filter->execute($field, $data);

        $this->assertSame('#FF0000', $data->get('color'));
    }
}
