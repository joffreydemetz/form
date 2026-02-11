<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Textarea;
use JDZ\Form\FormData;

class TextareaFieldTest extends TestCase
{
    public function testRendererIsTextarea(): void
    {
        $field = new Textarea('content');
        $data = $field->toData();
        $this->assertSame('textarea', $data['renderer']);
    }

    public function testSetPlaceholder(): void
    {
        $field = new Textarea('content');
        $field->setPlaceholder('Enter content...');
        $this->assertSame('Enter content...', $field->placeholder);
    }

    public function testSetRows(): void
    {
        $field = new Textarea('content');
        $field->setRows(10);
        $this->assertSame(10, $field->rows);
    }

    public function testSetCols(): void
    {
        $field = new Textarea('content');
        $field->setCols(50);
        $this->assertSame(50, $field->cols);
    }

    public function testSetMaxlength(): void
    {
        $field = new Textarea('content');
        $field->setMaxlength(500);
        $this->assertSame(500, $field->maxlength);
    }

    public function testToDataIncludesContent(): void
    {
        $field = new Textarea('content');
        $field->setValue('Hello World');
        $data = $field->toData();

        $this->assertSame('Hello World', $data['content']);
    }
}
