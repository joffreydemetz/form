<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\ColorFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class ColorFilterTest extends TestCase
{
    private ColorFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new ColorFilter();
    }

    public function testValidHexColorWith6Chars(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '#ff0000']);

        $this->filter->execute($field, $data);

        $this->assertSame('#FF0000', $data->get('test'));
    }

    public function testValidHexColorWith3Chars(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '#abc']);

        $this->filter->execute($field, $data);

        $this->assertSame('#ABC', $data->get('test'));
    }

    public function testDefaultsToBlackOnInvalid(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'notacolor']);

        $this->filter->execute($field, $data);

        $this->assertSame('#000000', $data->get('test'));
    }

    public function testUppercasesValue(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '#aabbcc']);

        $this->filter->execute($field, $data);

        $this->assertSame('#AABBCC', $data->get('test'));
    }

    public function testHandlesNoHash(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => 'ff0000']);

        $this->filter->execute($field, $data);

        $this->assertSame('#FF0000', $data->get('test'));
    }
}
