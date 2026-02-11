<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\StringFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class StringFilterTest extends TestCase
{
    private StringFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new StringFilter();
    }

    public function testName(): void
    {
        $this->assertSame('raw', $this->filter->name);
    }

    public function testTrimsWhitespace(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '  hello  ']);

        $this->filter->execute($field, $data);

        $this->assertSame('hello', $data->get('test'));
    }

    public function testStripsHtmlTags(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '<b>bold</b>']);

        $this->filter->execute($field, $data);

        $this->assertStringNotContainsString('<b>', $data->get('test'));
    }

    public function testRemovesScriptTags(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '<script>alert("xss")</script>hello']);

        $this->filter->execute($field, $data);

        $this->assertStringNotContainsString('<script>', $data->get('test'));
        $this->assertStringContainsString('hello', $data->get('test'));
    }

    public function testHandlesEmptyString(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '']);

        $this->filter->execute($field, $data);

        $this->assertSame('', $data->get('test'));
    }

    public function testHandlesPlainText(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'plain text']);

        $this->filter->execute($field, $data);

        $this->assertSame('plain text', $data->get('test'));
    }

    public function testSetsFieldValue(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '  trimmed  ']);

        $this->filter->execute($field, $data);

        $this->assertSame('trimmed', $field->value);
    }
}
