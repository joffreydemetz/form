<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\TimeFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class TimeFilterTest extends TestCase
{
    private TimeFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new TimeFilter();
    }

    public function testValidTime(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '14:30']);

        $this->filter->execute($field, $data);

        $this->assertSame('14:30', $data->get('test'));
    }

    public function testInvalidHoursDefaultsToZero(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '25:30']);

        $this->filter->execute($field, $data);

        $this->assertSame('00:30', $data->get('test'));
    }

    public function testInvalidMinutesDefaultsToZero(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '14:70']);

        $this->filter->execute($field, $data);

        $this->assertSame('14:00', $data->get('test'));
    }

    public function testMidnightTime(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '00:00']);

        $this->filter->execute($field, $data);

        $this->assertSame('00:00', $data->get('test'));
    }

    public function testMaxValidTime(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '23:59']);

        $this->filter->execute($field, $data);

        $this->assertSame('23:59', $data->get('test'));
    }
}
