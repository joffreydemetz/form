<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\LtRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class LtRuleTest extends TestCase
{
    public function testPassesWhenValueIsLess(): void
    {
        $rule = new LtRule();
        $rule->setCompareTo('max');

        $field = new Text('min');
        $field->setValue('3');
        $data = new FormData(['max' => '10', 'min' => '3']);

        $rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsWhenValueIsNotLess(): void
    {
        $this->expectException(InvalidException::class);

        $rule = new LtRule();
        $rule->setCompareTo('max');

        $field = new Text('min');
        $field->setValue('15');
        $data = new FormData(['max' => '10', 'min' => '15']);

        $rule->execute($field, $data);
    }

    public function testThrowsWhenValuesAreEqual(): void
    {
        $this->expectException(InvalidException::class);

        $rule = new LtRule();
        $rule->setCompareTo('max');

        $field = new Text('min');
        $field->setValue('10');
        $data = new FormData(['max' => '10', 'min' => '10']);

        $rule->execute($field, $data);
    }
}
