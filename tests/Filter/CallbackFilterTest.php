<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Filter;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Filter\CallbackFilter;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;

class CallbackFilterTest extends TestCase
{
    public function testExecutesCallbackWithFieldAndData(): void
    {
        $filter = new CallbackFilter([
            'callback' => function ($field, $data) {
                $data->set($field->getName(), strtoupper($data->get($field->getName())));
            },
        ]);
        $field = new Text('test');
        $data = new FormData(['test' => 'hello']);

        $filter->execute($field, $data);

        $this->assertSame('HELLO', $data->get('test'));
    }

    public function testCallbackIsInvoked(): void
    {
        $called = false;
        $filter = new CallbackFilter([
            'callback' => function ($field, $data) use (&$called) {
                $called = true;
            },
        ]);
        $field = new Text('test');
        $data = new FormData(['test' => 'hello']);

        $filter->execute($field, $data);

        $this->assertTrue($called);
    }
}
