<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Checkbox;
use JDZ\Form\FormData;

class CheckboxFieldTest extends TestCase
{
    public function testTypeIsCheckbox(): void
    {
        $field = new Checkbox('agree');
        $this->assertSame('checkbox', $field->type);
    }

    public function testRendererIsCheckbox(): void
    {
        $field = new Checkbox('agree');
        $data = $field->toData();
        $this->assertSame('checkbox', $data['renderer']);
    }

    public function testSetCheckboxLabel(): void
    {
        $field = new Checkbox('agree');
        $field->setCheckboxLabel('I agree');
        $data = $field->toData();
        $this->assertSame('I agree', $data['label']);
    }

    public function testSetCheckboxTip(): void
    {
        $field = new Checkbox('agree');
        $field->setCheckboxTip('Please agree');
        $data = $field->toData();
        $this->assertSame('Please agree', $data['tip']);
    }

    public function testCheckedState(): void
    {
        $field = new Checkbox('agree');
        $field->withChecked();
        $this->assertTrue($field->checked);
    }

    public function testDefaultImmutable(): void
    {
        $field = new Checkbox('agree');
        $this->assertTrue($field->immutable);
    }

    public function testSetCheckboxValue(): void
    {
        $field = new Checkbox('agree');
        $field->setCheckboxValue('yes');
        $this->assertSame('yes', $field->value);
    }
}
