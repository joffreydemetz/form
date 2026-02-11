<?php
declare(strict_types=1);

namespace JDZ\Form\Tests;

use PHPUnit\Framework\TestCase;
use JDZ\Form\FormButton;

class FormButtonTest extends TestCase
{
    public function testConstructSetsName(): void
    {
        $button = new FormButton('submit');
        $this->assertSame('submit', $button->getName());
    }

    public function testSetText(): void
    {
        $button = new FormButton('submit');
        $button->setText('Submit Form');

        $data = $button->toData();
        $this->assertStringContainsString('Submit Form', $data['text']);
    }

    public function testSetIcon(): void
    {
        $button = new FormButton('submit');
        $button->setText('Submit');
        $button->setIcon('icon-check');

        $data = $button->toData();
        $this->assertStringContainsString('icon-check', $data['text']);
    }

    public function testHasBtnStyle(): void
    {
        $button = new FormButton('submit');
        $data = $button->toData();
        $this->assertStringContainsString('btn', $data['attrs']['class'] ?? '');
    }

    public function testDefaultTagIsAnchor(): void
    {
        $button = new FormButton('submit');
        $data = $button->toData();
        $this->assertSame('a', $data['tag']);
    }
}
