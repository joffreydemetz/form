<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\DateFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class DateFilterTest extends TestCase
{
    private DateFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new DateFilter(['format' => 'Y-m-d']);
    }

    public function testValidDate(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '2024-06-15']);

        $this->filter->execute($field, $data);

        $this->assertSame('2024-06-15', $data->get('test'));
    }

    public function testInvalidDateZeros(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '0000-00-00']);

        $this->filter->execute($field, $data);

        $this->assertSame('', $data->get('test'));
    }

    public function testInvalidDateMinimum(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '1000-01-01']);

        $this->filter->execute($field, $data);

        $this->assertSame('', $data->get('test'));
    }

    public function testEmptyString(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '']);

        $this->filter->execute($field, $data);

        $this->assertSame('', $data->get('test'));
    }

    public function testConvertsDatetimeLocalFormat(): void
    {
        $datetimeFilter = new DateFilter(['format' => 'Y-m-d H:i:s']);
        $field = new Text('test');
        $data = new FormData(['test' => '2024-06-15T14:30']);

        $datetimeFilter->execute($field, $data);

        $this->assertSame('2024-06-15 14:30:00', $data->get('test'));
    }
}
