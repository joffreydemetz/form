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
        $this->assertSame(FormInterface::METHOD_POST, $form->method);
    }

    public function testSetAction(): void
    {
        $form = $this->createForm();
        $form->setAction('/submit');
        $this->assertSame('/submit', $form->action);
    }

    public function testSetMethod(): void
    {
        $form = $this->createForm();
        $form->setMethod(FormInterface::METHOD_GET);
        $this->assertSame('GET', $form->method);
    }

    public function testSetPrefix(): void
    {
        $form = $this->createForm();
        $form->setPrefix('jform');
        $this->assertSame('jform', $form->prefix);
    }

    public function testWithMultipart(): void
    {
        $form = $this->createForm();
        $form->withMultipart();
        $this->assertTrue($form->multipart);
    }

    public function testWithVertical(): void
    {
        $form = $this->createForm();
        $form->withVertical(false);
        $this->assertFalse($form->vertical);
    }

    public function testWithWide(): void
    {
        $form = $this->createForm();
        $form->withWide();
        $this->assertTrue($form->wide);
    }

    public function testWithCsrf(): void
    {
        $form = $this->createForm();
        $form->withCsrf();
        $this->assertTrue($form->csrf);
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
        $form->addField($row, 'main');

        $this->assertTrue($form->hasField('name'));
    }

    public function testAddFieldDefaultsToMainFieldset(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $row->setField(new Text('name'));
        $form->addField($row);

        $this->assertTrue($form->hasField('name', 'main'));
    }

    public function testRemoveField(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $row->setField(new Text('name'));
        $form->addField($row, 'main');

        $form->removeField('name');

        $this->assertFalse($form->hasField('name'));
    }

    public function testGetFieldThrowsWhenNotFound(): void
    {
        $this->expectException(FormException::class);

        $form = $this->createForm();
        $form->getField('nonexistent');
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

        $this->assertSame($data, $form->data);
    }

    public function testFilterAndValidate(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row = new FormRow('name');
        $field = new Text('name');
        $row->setField($field);
        $form->addField($row, 'main');

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
        $form->addField($row, 'main');

        $data = new FormData(['name' => 'John']);
        $form->setData($data);

        $result = $form->submit();
        $this->assertTrue($result);
        $this->assertEmpty($form->errors);
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
        $form->addField($row, 'main');

        $data = new FormData(['name' => '']);
        $form->setData($data);

        $result = $form->submit();
        $this->assertFalse($result);
        $this->assertNotEmpty($form->errors);
    }

    public function testGetFieldNames(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $row1 = new FormRow('name');
        $row1->setField(new Text('name'));
        $form->addField($row1, 'main');

        $row2 = new FormRow('email');
        $row2->setField(new Email('email'));
        $form->addField($row2, 'main');

        $names = $form->getFieldNames();
        $this->assertContains('name', $names);
        $this->assertContains('email', $names);
    }

    public function testHiddenFieldGoesToFieldrows(): void
    {
        $form = $this->createForm();
        $form->addFieldset(new FormFieldset('main'));

        $hiddenRow = new \JDZ\Form\FormRow\Hidden('id');
        $hiddenRow->setField(new Hidden('id'));
        $form->addField($hiddenRow);

        $this->assertTrue($form->hasField('id'));
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
