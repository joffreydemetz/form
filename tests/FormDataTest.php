<?php
declare(strict_types=1);

namespace JDZ\Form\Tests;

use PHPUnit\Framework\TestCase;
use JDZ\Form\FormData;

class FormDataTest extends TestCase
{
    public function testConstructWithData(): void
    {
        $data = new FormData(['name' => 'John', 'age' => 30]);

        $this->assertSame('John', $data->get('name'));
        $this->assertSame(30, $data->get('age'));
    }

    public function testConstructEmpty(): void
    {
        $data = new FormData();

        $this->assertEmpty($data->all());
    }

    public function testGetSet(): void
    {
        $data = new FormData();
        $data->set('name', 'Jane');

        $this->assertSame('Jane', $data->get('name'));
    }

    public function testHas(): void
    {
        $data = new FormData(['name' => 'John']);

        $this->assertTrue($data->has('name'));
        $this->assertFalse($data->has('email'));
    }

    public function testErase(): void
    {
        $data = new FormData(['name' => 'John', 'email' => 'john@example.com']);

        $data->erase('email');

        $this->assertFalse($data->has('email'));
        $this->assertTrue($data->has('name'));
    }

    public function testGetPropertiesAsObject(): void
    {
        $data = new FormData(['name' => 'John']);
        $props = $data->getProperties(true);

        $this->assertIsObject($props);
        $this->assertSame('John', $props->name);
    }

    public function testGetPropertiesAsArray(): void
    {
        $data = new FormData(['name' => 'John']);
        $props = $data->getProperties(false);

        $this->assertIsArray($props);
        $this->assertSame('John', $props['name']);
    }

    public function testExport(): void
    {
        $data = new FormData(['name' => 'John', 'age' => 30]);
        $export = $data->export();

        $this->assertIsArray($export);
        $this->assertSame('John', $export['name']);
        $this->assertSame(30, $export['age']);
    }

    public function testGetDefault(): void
    {
        $data = new FormData();

        $this->assertSame('default', $data->get('missing', 'default'));
    }
}
