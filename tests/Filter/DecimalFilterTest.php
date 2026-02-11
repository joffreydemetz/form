<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\DecimalFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class DecimalFilterTest extends TestCase
{
    public function testFormatsToOneDecimal(): void
    {
        $filter = new DecimalFilter();
        $field = new Text('test');
        $data = new FormData(['test' => '3.14159']);

        $filter->execute($field, $data);

        $this->assertSame('3.1', $data->get('test'));
    }

    public function testFormatsToTwoDecimals(): void
    {
        $filter = new DecimalFilter(['decimals' => 2]);
        $field = new Text('test');
        $data = new FormData(['test' => '3.14159']);

        $filter->execute($field, $data);

        $this->assertSame('3.14', $data->get('test'));
    }

    public function testHandlesIntegerInput(): void
    {
        $filter = new DecimalFilter(['decimals' => 2]);
        $field = new Text('test');
        $data = new FormData(['test' => '5']);

        $filter->execute($field, $data);

        $this->assertSame('5.00', $data->get('test'));
    }
}
