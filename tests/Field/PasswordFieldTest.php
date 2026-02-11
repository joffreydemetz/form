<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Password;
use JDZ\Form\FormData;

class PasswordFieldTest extends TestCase
{
    public function testTypeIsPassword(): void
    {
        $field = new Password('password');
        $this->assertSame('password', $field->type);
    }

    public function testAutocompleteIsNewPassword(): void
    {
        $field = new Password('password');
        $this->assertSame('new-password', $field->autocomplete);
    }

    public function testDefaultPasswordConfig(): void
    {
        $field = new Password('password');
        $this->assertSame(8, $field->pw['min']);
        $this->assertSame(20, $field->pw['max']);
    }

    public function testCustomPasswordConfig(): void
    {
        $field = new Password('password');
        $field->setPw(['min' => 10, 'max' => 30]);
        $this->assertSame(10, $field->pw['min']);
        $this->assertSame(30, $field->pw['max']);
    }

    public function testValidatesStrongPassword(): void
    {
        $field = new Password('password');
        $field->setPw(['min' => 8, 'max' => 20, 'upper' => 1, 'lower' => 1, 'digit' => 1, 'special' => 1]);
        $field->setValue('Str0ng!Pass');
        $data = new FormData(['password' => 'Str0ng!Pass']);

        $result = $field->validate($data);
        $this->assertTrue($result);
    }

    public function testRejectsWeakPassword(): void
    {
        $field = new Password('password');
        $field->setPw(['min' => 8, 'max' => 20, 'upper' => 1, 'lower' => 1, 'digit' => 1, 'special' => 1]);
        $field->setValue('weak');
        $data = new FormData(['password' => 'weak']);

        $result = $field->validate($data);
        $this->assertFalse($result);
    }
}
