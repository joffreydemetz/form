<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\IntFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class IntFilterTest extends TestCase
{
    public function testCastsToInt(): void
    {
        $filter = new IntFilter();
        $field = new Text('test');
        $data = new FormData(['test' => '42']);

        $filter->execute($field, $data);

        $this->assertSame(42, $data->get('test'));
    }

    public function testCastsStringToInt(): void
    {
        $filter = new IntFilter();
        $field = new Text('test');
        $data = new FormData(['test' => 'abc']);

        $filter->execute($field, $data);

        $this->assertSame(0, $data->get('test'));
    }

    public function testUnsignedAppliesAbs(): void
    {
        $filter = new IntFilter();
        $field = new Text('test');
        $data = new FormData(['test' => '-5']);

        $filter->execute($field, $data);

        $this->assertSame(5, $data->get('test'));
    }

    public function testSignedAllowsNegative(): void
    {
        $filter = new IntFilter();
        $filter->withUnsigned(false);
        $field = new Text('test');
        $data = new FormData(['test' => '-5']);

        $filter->execute($field, $data);

        $this->assertSame(-5, $data->get('test'));
    }

    public function testFloatMode(): void
    {
        $filter = new IntFilter();
        $filter->withFloat(true);
        $field = new Text('test');
        $data = new FormData(['test' => '3.14']);

        $filter->execute($field, $data);

        $this->assertSame(3.14, $data->get('test'));
    }

    public function testUnsignedFloatMode(): void
    {
        $filter = new IntFilter();
        $filter->withFloat(true);
        $field = new Text('test');
        $data = new FormData(['test' => '-3.14']);

        $filter->execute($field, $data);

        $this->assertSame(3.14, $data->get('test'));
    }
}
