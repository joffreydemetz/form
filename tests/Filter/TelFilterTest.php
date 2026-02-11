<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\TelFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class TelFilterTest extends TestCase
{
    private TelFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new TelFilter();
    }

    public function testKeepsDigits(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '0123456789']);

        $this->filter->execute($field, $data);

        $this->assertSame('0123456789', $data->get('test'));
    }

    public function testKeepsAllowedCharacters(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '+33(0)1.23.45.67.89']);

        $this->filter->execute($field, $data);

        $this->assertSame('+33(0)1.23.45.67.89', $data->get('test'));
    }

    public function testRemovesInvalidCharacters(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'abc123def456']);

        $this->filter->execute($field, $data);

        $this->assertSame('123456', $data->get('test'));
    }

    public function testRemovesSpaces(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '01 23 45 67 89']);

        $this->filter->execute($field, $data);

        $this->assertSame('0123456789', $data->get('test'));
    }
}
