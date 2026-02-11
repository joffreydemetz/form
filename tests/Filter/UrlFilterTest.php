<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\UrlFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class UrlFilterTest extends TestCase
{
    private UrlFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new UrlFilter();
    }

    public function testFullUrl(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'https://example.com/path']);

        $this->filter->execute($field, $data);

        $this->assertSame('https://example.com/path', $data->get('test'));
    }

    public function testAddsDefaultScheme(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'example.com']);

        $this->filter->execute($field, $data);

        $result = $data->get('test');
        $this->assertStringStartsWith('http', $result);
    }

    public function testHandlesEmptyString(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '']);

        $this->filter->execute($field, $data);

        $this->assertSame('', $data->get('test'));
    }

    public function testPreservesHttpsScheme(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'https://secure.example.com']);

        $this->filter->execute($field, $data);

        $this->assertStringStartsWith('https://', $data->get('test'));
    }
}
