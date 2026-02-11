<?php
declare(strict_types=1);

namespace JDZ\Form\Tests;

use PHPUnit\Framework\TestCase;
use JDZ\Form\SelectFieldOptgroup;
use JDZ\Form\SelectFieldOption;

class SelectFieldOptgroupTest extends TestCase
{
    public function testConstructSetsLabel(): void
    {
        $group = new SelectFieldOptgroup('Europe');
        $this->assertSame('Europe', $group->label);
    }

    public function testConstructWithDisabled(): void
    {
        $group = new SelectFieldOptgroup('Europe', true);
        $this->assertTrue($group->disabled);
    }

    public function testDefaultNotDisabled(): void
    {
        $group = new SelectFieldOptgroup('Europe');
        $this->assertFalse($group->disabled);
    }

    public function testSetOptions(): void
    {
        $group = new SelectFieldOptgroup('Europe');
        $group->setOptions([
            new SelectFieldOption('fr', 'France'),
            new SelectFieldOption('de', 'Germany'),
        ]);

        $this->assertCount(2, $group->options);
    }

    public function testWithDisabled(): void
    {
        $group = new SelectFieldOptgroup('Europe');
        $group->withDisabled(true);
        $this->assertTrue($group->disabled);
    }

    public function testToDataIncludesLabelAndOptions(): void
    {
        $group = new SelectFieldOptgroup('Europe');
        $group->setOptions([
            new SelectFieldOption('fr', 'France'),
        ]);

        $data = $group->toData();

        $this->assertSame('Europe', $data['label']);
        $this->assertCount(1, $data['options']);
    }
}
