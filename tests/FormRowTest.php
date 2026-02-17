<?php
declare(strict_types=1);

namespace JDZ\Form\Tests;

use PHPUnit\Framework\TestCase;
use JDZ\Form\FormRow;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class FormRowTest extends TestCase
{
    public function testConstructSetsName(): void
    {
        $row = new FormRow('name');
        $this->assertSame('name', $row->getName());
    }

    public function testSetLabelText(): void
    {
        $row = new FormRow('name');
        $row->setLabelText('Full Name');
        $this->assertSame('Full Name', $row->getLabelText());
    }

    public function testSetTip(): void
    {
        $row = new FormRow('name');
        $row->setTip('Enter your full name');
        $this->assertSame('Enter your full name', $row->getTip());
    }

    public function testSetField(): void
    {
        $row = new FormRow('name');
        $field = new Text('name');
        $row->setField($field);

        $this->assertSame($field, $row->getField());
    }

    public function testSetValueDelegatesToField(): void
    {
        $row = new FormRow('name');
        $field = new Text('name');
        $row->setField($field);

        $row->setValue('John');

        $this->assertSame('John', $field->value);
    }

    public function testWithRequired(): void
    {
        $row = new FormRow('name');
        $row->withRequired();
        $this->assertTrue($row->isRequired());
    }

    public function testWithDisabled(): void
    {
        $row = new FormRow('name');
        $row->withDisabled();
        $this->assertTrue($row->isDisabled());
    }

    public function testWithReadonly(): void
    {
        $row = new FormRow('name');
        $row->withReadonly();
        $this->assertTrue($row->isReadonly());
    }

    public function testFilter(): void
    {
        $row = new FormRow('name');
        $field = new Text('name');
        $row->setField($field);

        $data = new FormData(['name' => '  John  ']);
        $row->filter($data);

        $this->assertSame('John', $data->get('name'));
    }

    public function testValidateSuccess(): void
    {
        $row = new FormRow('name');
        $field = new Text('name');
        $row->setField($field);

        $data = new FormData(['name' => 'John']);
        $field->setValue('John');

        $result = $row->validate($data);
        $this->assertTrue($result);
    }

    public function testValidateFailureAddsErrors(): void
    {
        $row = new FormRow('name');
        $row->setLabelText('Name');
        $row->withRequired();

        $field = new Text('name');
        $row->setField($field);
        $field->setValue('');

        $data = new FormData(['name' => '']);

        $result = $row->validate($data);
        $this->assertFalse($result);
        $this->assertNotEmpty($row->getErrors());
    }

    public function testOnFillValues(): void
    {
        $row = new FormRow('name');
        $field = new Text('name');
        $row->setField($field);

        $data = new FormData(['name' => 'John']);
        $row->onFillValues($data);

        $this->assertSame('John', $field->value);
    }

    public function testToDataIncludesLabel(): void
    {
        $row = new FormRow('name');
        $row->setLabelText('Name');
        $field = new Text('name');
        $row->setField($field);

        $data = $row->toData();

        $this->assertSame('Name', $data['label']);
    }

    public function testToDataIncludesTip(): void
    {
        $row = new FormRow('name');
        $row->setTip('A helpful tip');
        $field = new Text('name');
        $row->setField($field);

        $data = $row->toData();

        $this->assertSame('A helpful tip', $data['tip']);
    }

    public function testBubblesRequiredToField(): void
    {
        $row = new FormRow('name');
        $row->withRequired();
        $field = new Text('name');
        $row->setField($field);

        $data = new FormData(['name' => 'John']);
        $row->filter($data);

        $this->assertTrue($field->required);
    }

    public function testBubblesDisabledToField(): void
    {
        $row = new FormRow('name');
        $row->withDisabled();
        $field = new Text('name');
        $row->setField($field);

        $data = new FormData(['name' => 'John']);
        $row->filter($data);

        $this->assertTrue($field->disabled);
    }

    public function testBubblesPrefixToField(): void
    {
        $row = new FormRow('name');
        $row->setPrefix('form');
        $field = new Text('name');
        $row->setField($field);

        $data = new FormData(['name' => 'John']);
        $row->filter($data);

        $this->assertSame('form', $field->prefix);
    }

    public function testRendererType(): void
    {
        $row = new FormRow('name');
        $this->assertSame('form.field', $row->getRenderer());
    }

    public function testFluentApi(): void
    {
        $row = new FormRow('name');

        $result = $row->setLabelText('Name')
            ->setTip('Enter name')
            ->withRequired()
            ->withDisabled()
            ->withReadonly()
            ->withOffset()
            ->withLabel()
            ->withLabelOnTop()
            ->withTipOnTop();

        $this->assertSame($row, $result);
    }
}
