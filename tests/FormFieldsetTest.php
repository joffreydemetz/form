<?php
declare(strict_types=1);

namespace JDZ\Form\Tests;

use PHPUnit\Framework\TestCase;
use JDZ\Form\FormFieldset;
use JDZ\Form\FormRow;
use JDZ\Form\Field\Text;
use JDZ\Form\Exception\FormException;

class FormFieldsetTest extends TestCase
{
    public function testConstructSetsName(): void
    {
        $fieldset = new FormFieldset('main');
        $this->assertSame('main', $fieldset->getName());
    }

    public function testSetLabel(): void
    {
        $fieldset = new FormFieldset('details');
        $fieldset->setLabel('Personal Details');
        $this->assertSame('Personal Details', $fieldset->label);
    }

    public function testSetDescription(): void
    {
        $fieldset = new FormFieldset('details');
        $fieldset->setDescription('Enter your personal information');
        $this->assertSame('Enter your personal information', $fieldset->description);
    }

    public function testAddAndGetField(): void
    {
        $fieldset = new FormFieldset('main');
        $row = new FormRow('name');
        $row->setField(new Text('name'));

        $fieldset->addField($row);

        $this->assertTrue($fieldset->hasField('name'));
        $this->assertSame($row, $fieldset->getField('name'));
    }

    public function testRemoveField(): void
    {
        $fieldset = new FormFieldset('main');
        $row = new FormRow('name');
        $row->setField(new Text('name'));
        $fieldset->addField($row);

        $fieldset->removeField('name');

        $this->assertFalse($fieldset->hasField('name'));
    }

    public function testGetFieldThrowsWhenNotFound(): void
    {
        $this->expectException(FormException::class);

        $fieldset = new FormFieldset('main');
        $fieldset->getField('nonexistent');
    }

    public function testFieldPositioning(): void
    {
        $fieldset = new FormFieldset('main');

        $row1 = new FormRow('first');
        $row1->setField(new Text('first'));
        $fieldset->addField($row1);

        $row2 = new FormRow('second');
        $row2->setField(new Text('second'));
        $fieldset->addField($row2);

        $this->assertSame(1, $row1->getPosition());
        $this->assertSame(2, $row2->getPosition());
    }

    public function testSetFieldPositions(): void
    {
        $fieldset = new FormFieldset('main');

        $row1 = new FormRow('a');
        $row1->setField(new Text('a'));
        $fieldset->addField($row1);

        $row2 = new FormRow('b');
        $row2->setField(new Text('b'));
        $fieldset->addField($row2);

        $fieldset->setFieldPositions(['b', 'a']);

        $this->assertSame(2, $row1->getPosition());
        $this->assertSame(1, $row2->getPosition());
    }

    public function testOnPrepareRemovesEmptyFieldset(): void
    {
        $fieldset = new FormFieldset('empty');

        $result = $fieldset->onPrepare();

        $this->assertFalse($result);
    }

    public function testOnPrepareKeepsNonEmptyFieldset(): void
    {
        $fieldset = new FormFieldset('main');
        $row = new FormRow('name');
        $row->setField(new Text('name'));
        $fieldset->addField($row);

        $result = $fieldset->onPrepare();

        $this->assertTrue($result);
    }

    public function testToDataIncludesLabelAndDescription(): void
    {
        $fieldset = new FormFieldset('details');
        $fieldset->setLabel('Details');
        $fieldset->setDescription('Some description');

        $data = $fieldset->toData();

        $this->assertSame('Details', $data['label']);
        $this->assertSame('Some description', $data['description']);
    }

    public function testToDataReplacesDotsInName(): void
    {
        $fieldset = new FormFieldset('some.name');
        $data = $fieldset->toData();

        $this->assertSame('some-name', $data['name']);
    }
}
