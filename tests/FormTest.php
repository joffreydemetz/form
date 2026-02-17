<?php
declare(strict_types=1);

namespace JDZ\Form\Tests;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Form;
use JDZ\Form\FormData;
use JDZ\Form\FormFieldset;
use JDZ\Form\FormRow;
use JDZ\Form\FormButton;
use JDZ\Form\Field\Text;
use JDZ\Form\Field\Email;
use JDZ\Form\Field\Hidden;
use JDZ\Form\Contract\FormInterface;
use JDZ\Form\Exception\FormException;

class FormTest extends TestCase
{
    private function createForm(string $name = 'test_form'): Form
    {
        return new Form($name);
    }

    public function testConstructSetsName(): void
    {
        $form = $this->createForm('myform');
        $this->assertSame('myform', $form->getName());
    }

    public function testDefaultMethod(): void
    {
        $form = $this->createForm();
        $this->assertSame(FormInterface::METHOD_POST, $form->getMethod());
    }

    public function testSetAction(): void
    {
        $form = $this->createForm();
        $form->setAction('/submit');
        $this->assertSame('/submit', $form->getAction());
    }

    public function testSetMethod(): void
    {
        $form = $this->createForm();
        $form->setMethod(FormInterface::METHOD_GET);
        $this->assertSame('GET', $form->getMethod());
    }

    public function testSetPrefix(): void
    {
        $form = $this->createForm();
        $form->setPrefix('jform');
        $this->assertSame('jform', $form->getPrefix());
    }

    public function testWithMultipart(): void
    {
        $form = $this->createForm();
        $form->withMultipart();
        $this->assertTrue($form->isMultipart());
    }

    public function testWithVertical(): void
    {
        $form = $this->createForm();
        $form->withVertical(false);
        $this->assertFalse($form->isVertical());
    }

    public function testWithWide(): void
    {
        $form = $this->createForm();
        $form->withWide();
        $this->assertTrue($form->isWide());
    }

    public function testWithCsrf(): void
    {
        $form = $this->createForm();
        $form->withCsrf();
        $this->assertTrue($form->usesCsrf());
    }

    public function testWithCaptcha(): void
    {
        $form = $this->createForm();
        $form->withCaptcha();
        $this->assertTrue($form->usesCaptcha());
    }

    public function testMakeFormFieldset(): void
    {
        $form = $this->createForm();
        $fieldset = $form->makeFormFieldset('details');
        $this->assertSame('details', $fieldset->getName());
    }

    public function testAddAndGetFieldset(): void
    {
        $form = $this->createForm();
        $fieldset = new FormFieldset('details');
        $form->addFieldset($fieldset);

        $this->assertTrue($form->hasFieldset('details'));
        $this->assertSame($fieldset, $form->getFieldset('details'));
    }

    public function testRemoveFieldset(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('details'));
        $form->removeFieldset('details');

        $this->assertFalse($form->hasFieldset('details'));
    }

    public function testGetFieldsetThrowsWhenNotFound(): void
    {
        $this->expectException(FormException::class);

        $form = $this->createForm();
        $form->getFieldset('nonexistent');
    }

    public function testAddFieldToFieldset(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $row->setField(new Text('name'));
        $form->addFormRow($row, 'main');

        $this->assertTrue($form->hasFormRow('name'));
    }

    public function testAddFieldDefaultsToMainFieldset(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $row->setField(new Text('name'));
        $form->addFormRow($row);

        $this->assertTrue($form->hasFormRow('name', 'main'));
    }

    public function testRemoveField(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $row->setField(new Text('name'));
        $form->addFormRow($row, 'main');

        $form->removeFormRow('name');

        $this->assertFalse($form->hasFormRow('name'));
    }

    public function testGetFieldThrowsWhenNotFound(): void
    {
        $this->expectException(FormException::class);

        $form = $this->createForm();
        $form->getFormRow('nonexistent');
    }

    public function testAddAndGetButton(): void
    {
        $form = $this->createForm();
        $button = new FormButton('submit');
        $button->setText('Submit');
        $form->addButton($button);

        $this->assertTrue($form->hasButton('submit'));
        $this->assertSame($button, $form->getButton('submit'));
    }

    public function testRemoveButton(): void
    {
        $form = $this->createForm();
        $form->addButton(new FormButton('submit'));
        $form->removeButton('submit');

        $this->assertFalse($form->hasButton('submit'));
    }

    public function testGetButtonThrowsWhenNotFound(): void
    {
        $this->expectException(FormException::class);

        $form = $this->createForm();
        $form->getButton('nonexistent');
    }

    public function testInit(): void
    {
        $form = $this->createForm();
        $data = new FormData(['name' => 'John']);

        $form->init($data);

        $this->assertSame($data, $form->getData());
    }

    public function testFilterAndValidate(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $field = new Text('name');
        $row->setField($field);
        $form->addFormRow($row, 'main');

        $data = new FormData(['name' => '  John  ']);
        $form->setData($data);

        $form->filter();

        $this->assertSame('John', $data->get('name'));
    }

    public function testSubmitWithValidData(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $field = new Text('name');
        $field->setValue('John');
        $row->setField($field);
        $form->addFormRow($row, 'main');

        $data = new FormData(['name' => 'John']);
        $form->setData($data);

        $result = $form->submit();
        $this->assertTrue($result);
        $this->assertEmpty($form->getErrors());
    }

    public function testSubmitWithInvalidData(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $row->withRequired();
        $field = new Text('name');
        $field->setValue('');
        $row->setField($field);
        $form->addFormRow($row, 'main');

        $data = new FormData(['name' => '']);
        $form->setData($data);

        $result = $form->submit();
        $this->assertFalse($result);
        $this->assertNotEmpty($form->getErrors());
    }

    public function testGetFieldNames(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row1 = new FormRow('name');
        $row1->setField(new Text('name'));
        $form->addFormRow($row1, 'main');

        $row2 = new FormRow('email');
        $row2->setField(new Email('email'));
        $form->addFormRow($row2, 'main');

        $names = $form->getFormRowNames();
        $this->assertContains('name', $names);
        $this->assertContains('email', $names);
    }

    public function testHiddenFieldGoesToFieldrows(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $hiddenRow = new \JDZ\Form\FormRow\Hidden('id');
        $hiddenRow->setField(new Hidden('id'));
        $form->addFormRow($hiddenRow);

        $this->assertTrue($form->hasFormRow('id'));
    }

    public function testFluentApi(): void
    {
        $form = $this->createForm();

        $result = $form->setAction('/submit')
            ->setMethod(FormInterface::METHOD_POST)
            ->setPrefix('jform')
            ->withMultipart()
            ->withVertical()
            ->withWide()
            ->withCsrf()
            ->withCaptcha();

        $this->assertSame($form, $result);
    }
}
