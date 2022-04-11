<?php declare(strict_types=1);

namespace Tests\All\Entry;

use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Entry\Entry;
use Tests\All\AbstractUnitTestCase;

class EntryTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_test_create_work(): void
    {
        $entry = new Entry(StringVo::fromValue('value'));

        $this->assertEquals('value', $entry->get());
    }

    /** @test */
    public function it_equal_work(): void
    {
        $entry = new Entry(StringVo::fromValue('value'));
        $entry2 = new Entry(StringVo::fromValue('value'));

        $this->assertTrue($entry->equalTo($entry2));
    }

    /** @test */
    public function it_equal_return_should_return_false_if_not_equal(): void
    {
        $entry = new Entry(StringVo::fromValue('value'));
        $entry2 = new Entry(StringVo::fromValue('different value'));

        $this->assertFalse($entry->equalTo($entry2));
    }
}
