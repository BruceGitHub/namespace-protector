<?php

namespace Tests\Unit\Entry;

use NamespaceProtector\Entry\Entry;
use Tests\Unit\AbstractUnitTestCase;

class EntryTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_test_create_work(): void
    {
        $entry = new Entry('value');

        $this->assertEquals('value', $entry->get());
    }

    /** @test */
    public function it_equal_work(): void
    {
        $entry = new Entry('value');
        $entry2 = new Entry('value');

        $this->assertTrue($entry->equalTo($entry2));
    }

    /** @test */
    public function it_equal_return_should_return_false_if_not_equal(): void
    {
        $entry = new Entry('value');
        $entry2 = new Entry('different value');

        $this->assertFalse($entry->equalTo($entry2));
    }
}
