<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class TextFieldTest extends TestCase
{
    public function testDefaultMaxlength(): void
    {
        $field = new Text('name');
        $this->assertSame(250, $field->maxlength);
    }

    public function testSetValue(): void
    {
        $field = new Text('name');
        $field->setValue('John');
        $this->assertSame('John', $field->value);
    }

    public function testSetPlaceholder(): void
    {
        $field = new Text('name');
        $field->setPlaceholder('Enter your name');
        $this->assertSame('Enter your name', $field->placeholder);
    }

    public function testFilter(): void
    {
        $field = new Text('name');
        $data = new FormData(['name' => '  John  ']);

        $field->filter($data);

        $this->assertSame('John', $data->get('name'));
        $this->assertSame('John', $field->value);
    }

    public function testValidateRequired(): void
    {
        $field = new Text('name');
        $field->withRequired();
        $field->setValue('John');
        $data = new FormData(['name' => 'John']);

        $result = $field->validate($data);
        $this->assertTrue($result);
    }

    public function testValidateRequiredFailsWhenEmpty(): void
    {
        $field = new Text('name');
        $field->withRequired();
        $field->setValue('');
        $data = new FormData(['name' => '']);

        $result = $field->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($field->errors);
    }

    public function testImmutableFieldCannotBeChanged(): void
    {
        $field = new Text('name');
        $field->setValue('original');
        $field->withImmutable();
        $field->setValue('changed');

        $this->assertSame('original', $field->value);
    }

    public function testDisabledFieldErasesData(): void
    {
        $field = new Text('name');
        $field->withDisabled();
        $data = new FormData(['name' => 'John']);

        $field->filter($data);

        $this->assertNull($data->get('name'));
    }

    public function testFluentApi(): void
    {
        $field = new Text('name');

        $result = $field->setPlaceholder('Name')
            ->setMaxlength(100)
            ->withRequired()
            ->withDisabled()
            ->withReadonly();

        $this->assertSame($field, $result);
    }

    public function testOnFillValues(): void
    {
        $field = new Text('name');
        $data = new FormData(['name' => 'John']);

        $field->onFillValues($data);

        $this->assertSame('John', $field->value);
    }

    public function testOnFillValuesUsesDefault(): void
    {
        $field = new Text('name');
        $field->setDefault('default_name');
        $data = new FormData([]);

        $field->onFillValues($data);

        $this->assertSame('default_name', $field->value);
    }

    public function testRendererType(): void
    {
        $field = new Text('name');
        $data = $field->toData();
        $this->assertSame('input', $data['renderer']);
    }
}
