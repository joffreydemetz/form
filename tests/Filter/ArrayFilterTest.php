<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\ArrayFilter;

class ArrayFilterTest extends TestCase
{
    public function testName(): void
    {
        $filter = new ArrayFilter();
        $this->assertSame('array', $filter->name);
    }

    public function testExtendsStringFilter(): void
    {
        $filter = new ArrayFilter();
        $this->assertInstanceOf(\JDZ\Form\Filter\StringFilter::class, $filter);
    }
}
