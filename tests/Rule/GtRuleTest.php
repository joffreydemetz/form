<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\GtRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class GtRuleTest extends TestCase
{
    public function testPassesWhenValueIsGreater(): void
    {
        $rule = new GtRule();
        $rule->setCompareTo('min');

        $field = new Text('max');
        $field->setValue('10');
        $data = new FormData(['min' => '5', 'max' => '10']);

        $rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsWhenValueIsNotGreater(): void
    {
        $this->expectException(InvalidException::class);

        $rule = new GtRule();
        $rule->setCompareTo('min');

        $field = new Text('max');
        $field->setValue('3');
        $data = new FormData(['min' => '5', 'max' => '3']);

        $rule->execute($field, $data);
    }

    public function testThrowsWhenValuesAreEqual(): void
    {
        $this->expectException(InvalidException::class);

        $rule = new GtRule();
        $rule->setCompareTo('min');

        $field = new Text('max');
        $field->setValue('5');
        $data = new FormData(['min' => '5', 'max' => '5']);

        $rule->execute($field, $data);
    }
}
