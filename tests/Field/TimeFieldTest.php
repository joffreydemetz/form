<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Time;
use JDZ\Form\FormData;

class TimeFieldTest extends TestCase
{
    public function testTypeIsTime(): void
    {
        $field = new Time('start_time');
        $this->assertSame('time', $field->type);
    }

    public function testInitAddsFilters(): void
    {
        $field = new Time('start_time');
        $field->init();
        $this->assertNotEmpty($field->filters);
    }

    public function testMinProperty(): void
    {
        $field = new Time('start_time');
        $field->min = '08:00';
        $this->assertSame('08:00', $field->min);
    }

    public function testMaxProperty(): void
    {
        $field = new Time('start_time');
        $field->max = '18:00';
        $this->assertSame('18:00', $field->max);
    }
}
