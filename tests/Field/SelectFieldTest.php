<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Select;
use JDZ\Form\SelectFieldOption;
use JDZ\Form\SelectFieldOptgroup;
use JDZ\Form\FormData;

class SelectFieldTest extends TestCase
{
    public function testRendererIsSelect(): void
    {
        $field = new Select('country');
        $data = $field->toData();
        $this->assertSame('select', $data['renderer']);
    }

    public function testAddOption(): void
    {
        $field = new Select('country');
        $field->addOption(new SelectFieldOption('fr', 'France'));
        $field->addOption(new SelectFieldOption('us', 'United States'));

        $this->assertCount(2, $field->options);
    }

    public function testSetOptions(): void
    {
        $field = new Select('country');
        $field->setOptions([
            new SelectFieldOption('fr', 'France'),
            new SelectFieldOption('us', 'United States'),
        ]);

        $this->assertCount(2, $field->options);
    }

    public function testSetValue(): void
    {
        $field = new Select('country');
        $field->addOption(new SelectFieldOption('fr', 'France'));
        $field->setValue('fr');

        $this->assertSame('fr', $field->value);
        $this->assertContains('fr', $field->selected);
    }

    public function testMultipleSelection(): void
    {
        $field = new Select('tags');
        $field->withMultiple();
        $field->setValue('a,b,c');

        $this->assertCount(3, $field->selected);
    }

    public function testIsEmptyWhenNoSelection(): void
    {
        $field = new Select('country');
        $this->assertTrue($field->isEmpty());
    }

    public function testIsNotEmptyWhenSelected(): void
    {
        $field = new Select('country');
        $field->setValue('fr');
        $this->assertFalse($field->isEmpty());
    }

    public function testHasOptions(): void
    {
        $field = new Select('country');
        $field->addOption(new SelectFieldOption('fr', 'France'));
        $field->addOption(new SelectFieldOption('us', 'United States'));

        $this->assertTrue($field->hasOptions());
    }

    public function testWithNice(): void
    {
        $field = new Select('country');
        $field->withNice(true, 'Select country', 'Search...');

        $this->assertTrue($field->nice);
        $this->assertTrue($field->niceSearch);
        $this->assertSame('Select country', $field->nicePlaceholder);
        $this->assertSame('Search...', $field->niceSearchPlaceholder);
    }

    public function testOnFillValuesSelectsCorrectOption(): void
    {
        $field = new Select('country');
        $field->addOption(new SelectFieldOption('fr', 'France'));
        $field->addOption(new SelectFieldOption('us', 'United States'));

        $data = new FormData(['country' => 'us']);
        $field->onFillValues($data);

        $this->assertContains('us', $field->selected);
    }

    public function testToStaticReturnsReadableValue(): void
    {
        $field = new Select('country');
        $field->addOption(new SelectFieldOption('fr', 'France'));
        $field->addOption(new SelectFieldOption('us', 'United States'));
        $field->setValue('fr');

        $this->assertSame('France', $field->toStatic());
    }

    public function testOptgroup(): void
    {
        $field = new Select('country');
        $optgroup = new SelectFieldOptgroup('Europe');
        $optgroup->setOptions([
            new SelectFieldOption('fr', 'France'),
            new SelectFieldOption('de', 'Germany'),
        ]);
        $field->addOption($optgroup);

        $this->assertCount(1, $field->options);
    }

    public function testToDataIncludesOptions(): void
    {
        $field = new Select('country');
        $field->addOption(new SelectFieldOption('fr', 'France'));
        $data = $field->toData();

        $this->assertArrayHasKey('options', $data);
        $this->assertArrayHasKey('selected', $data);
    }
}
