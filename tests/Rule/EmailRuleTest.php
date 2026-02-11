<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Rule;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Rule\EmailRule;
use JDZ\Form\Field\Text;
use JDZ\Form\FormData;
use JDZ\Form\Exception\InvalidException;

class EmailRuleTest extends TestCase
{
    private EmailRule $rule;

    protected function setUp(): void
    {
        $this->rule = new EmailRule();
    }

    public function testPassesForValidEmail(): void
    {
        $field = new Text('email');
        $field->setValue('user@example.com');
        $data = new FormData(['email' => 'user@example.com']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testThrowsForInvalidEmail(): void
    {
        $this->expectException(InvalidException::class);

        $field = new Text('email');
        $field->setValue('not-an-email');
        $data = new FormData(['email' => 'not-an-email']);

        $this->rule->execute($field, $data);
    }

    public function testThrowsForEmailWithoutAt(): void
    {
        $this->expectException(InvalidException::class);

        $field = new Text('email');
        $field->setValue('userexample.com');
        $data = new FormData(['email' => 'userexample.com']);

        $this->rule->execute($field, $data);
    }

    public function testSkipsEmptyValue(): void
    {
        $field = new Text('email');
        $field->setValue('');
        $data = new FormData(['email' => '']);

        $this->rule->execute($field, $data);
        $this->assertTrue(true);
    }

    public function testNameIsEmail(): void
    {
        $this->assertSame('email', $this->rule->name);
    }
}
