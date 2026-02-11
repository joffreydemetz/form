<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\BoolFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class BoolFilterTest extends TestCase
{
    private BoolFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new BoolFilter();
    }

    public function testCastsTrueString(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '1']);

        $this->filter->execute($field, $data);

        $this->assertTrue($data->get('test'));
    }

    public function testCastsFalseString(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '']);

        $this->filter->execute($field, $data);

        $this->assertFalse($data->get('test'));
    }

    public function testCastsZeroToFalse(): void
    {
        $field = new Text('test');
        $data = new FormData(['test' => '0']);

        $this->filter->execute($field, $data);

        $this->assertFalse($data->get('test'));
    }
}
