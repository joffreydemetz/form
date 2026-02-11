<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\BotRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class BotRuleTest extends TestCase
{
    private BotRule $rule;

    protected function setUp(): void
    {
        $this->rule = new BotRule();
    }

    public function testPassesWhenFieldIsEmpty(): void
    {
        $field = new Text('honeypot');
        $field->setValue('');
        $data = new FormData(['honeypot' => '']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsWhenFieldHasValue(): void
    {
        $this->expectException(InvalidException::class);

        $field = new Text('honeypot');
        $field->setValue('bot-filled-this');
        $data = new FormData(['honeypot' => 'bot-filled-this']);

        $this->rule->execute($field, $data);
    }

    public function testNameIsBot(): void
    {
        $this->assertSame('bot', $this->rule->name);
    }
}
