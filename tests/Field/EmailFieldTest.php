<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Email;
use JDZ\Form\FormData;

class EmailFieldTest extends TestCase
{
    public function testTypeIsEmail(): void
    {
        $field = new Email('email');
        $this->assertSame('email', $field->type);
    }

    public function testAutocompleteIsEmail(): void
    {
        $field = new Email('email');
        $this->assertSame('email', $field->autocomplete);
    }

    public function testInitAddsFilters(): void
    {
        $field = new Email('email');
        $field->init();
        $this->assertNotEmpty($field->filters);
    }

    public function testValidatesEmailFormat(): void
    {
        $field = new Email('email');
        $field->init();
        $field->setValue('user@example.com');
        $data = new FormData(['email' => 'user@example.com']);

        $result = $field->validate($data);
        $this->assertTrue($result);
    }

    public function testRejectsInvalidEmail(): void
    {
        $field = new Email('email');
        $field->init();
        $field->setValue('not-an-email');
        $data = new FormData(['email' => 'not-an-email']);

        $result = $field->validate($data);
        $this->assertFalse($result);
    }

    public function testFilterSanitizesEmail(): void
    {
        $field = new Email('email');
        // Use EmailFilter directly to test sanitization
        $filter = new \JDZ\Form\Filter\EmailFilter();
        $data = new FormData(['email' => 'User@Example.COM']);

        $filter->execute($field, $data);

        $this->assertSame('user@example.com', $field->value);
    }
}
