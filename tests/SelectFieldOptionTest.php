<?php
declare(strict_types=1);

namespace JDZ\Form\Tests;

use PHPUnit\Framework\TestCase;
use JDZ\Form\SelectFieldOption;

class SelectFieldOptionTest extends TestCase
{
    public function testConstructSetsProperties(): void
    {
        $option = new SelectFieldOption('fr', 'France');

        $this->assertSame('fr', $option->value);
        $this->assertSame('France', $option->text);
    }

    public function testConstructWithSelected(): void
    {
        $option = new SelectFieldOption('fr', 'France', true);
        $this->assertTrue($option->selected);
    }

    public function testConstructWithDisabled(): void
    {
        $option = new SelectFieldOption('fr', 'France', false, true);
        $this->assertTrue($option->disabled);
    }

    public function testDefaultNotSelected(): void
    {
        $option = new SelectFieldOption('fr', 'France');
        $this->assertFalse($option->selected);
    }

    public function testDefaultNotDisabled(): void
    {
        $option = new SelectFieldOption('fr', 'France');
        $this->assertFalse($option->disabled);
    }

    public function testWithSelected(): void
    {
        $option = new SelectFieldOption('fr', 'France');
        $option->withSelected(true);
        $this->assertTrue($option->selected);
    }

    public function testWithDisabled(): void
    {
        $option = new SelectFieldOption('fr', 'France');
        $option->withDisabled(true);
        $this->assertTrue($option->disabled);
    }

    public function testSetValue(): void
    {
        $option = new SelectFieldOption('fr', 'France');
        $option->setValue('de');
        $this->assertSame('de', $option->value);
    }

    public function testSetText(): void
    {
        $option = new SelectFieldOption('fr', 'France');
        $option->setText('Germany');
        $this->assertSame('Germany', $option->text);
    }

    public function testSetSearchable(): void
    {
        $option = new SelectFieldOption('fr', 'France');
        $option->setSearchable('france french');
        $this->assertSame('france french', $option->searchable);
    }

    public function testToDataIncludesProperties(): void
    {
        $option = new SelectFieldOption('fr', 'France', true, false);
        $data = $option->toData();

        $this->assertSame('France', $data['text']);
        $this->assertTrue($data['selected']);
        $this->assertFalse($data['disabled']);
    }
}
