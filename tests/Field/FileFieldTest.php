<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\File;
use JDZ\Form\FormData;

class FileFieldTest extends TestCase
{
    public function testTypeIsFile(): void
    {
        $field = new File('avatar');
        $this->assertSame('file', $field->type);
    }

    public function testSetAccept(): void
    {
        $field = new File('avatar');
        $field->setAccept('image/*');

        $data = $field->toData();
        $this->assertStringContainsString('image/*', $data['attrs']['accept'] ?? '');
    }

    public function testWithMultiple(): void
    {
        $field = new File('photos');
        $field->withMultiple();

        $data = $field->toData();
        $this->assertSame('multiple', $data['attrs']['multiple'] ?? '');
    }
}
