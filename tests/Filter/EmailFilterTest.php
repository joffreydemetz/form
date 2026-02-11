<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\EmailFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class EmailFilterTest extends TestCase
{
    private EmailFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new EmailFilter();
    }

    public function testValidEmail(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'user@example.com']);

        $this->filter->execute($field, $data);

        $this->assertSame('user@example.com', $data->get('test'));
    }

    public function testLowercasesEmail(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'User@Example.COM']);

        $this->filter->execute($field, $data);

        $this->assertSame('user@example.com', $data->get('test'));
    }

    public function testStripsTagsFromEmail(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '<b>user</b>@example.com']);

        $this->filter->execute($field, $data);

        $this->assertStringNotContainsString('<b>', $data->get('test'));
    }

    public function testHandlesEmptyString(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '']);

        $this->filter->execute($field, $data);

        $this->assertSame('', $data->get('test'));
    }
}
