<?php
declare(strict_types=1);

namespace JDZ\Form\Tests\Field;

use PHPUnit\Framework\TestCase;
use JDZ\Form\Field\Url;
use JDZ\Form\FormData;

class UrlFieldTest extends TestCase
{
    public function testTypeIsUrl(): void
    {
        $field = new Url('website');
        $this->assertSame('url', $field->type);
    }

    public function testDefaultPlaceholder(): void
    {
        $field = new Url('website');
        $this->assertSame('https://', $field->placeholder);
    }

    public function testAutocompleteIsUrl(): void
    {
        $field = new Url('website');
        $this->assertSame('url', $field->autocomplete);
    }

    public function testInitAddsFilters(): void
    {
        $field = new Url('website');
        $field->init();
        $this->assertNotEmpty($field->filters);
    }
}
