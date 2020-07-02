<?php

namespace Tests\Unit\Db;

use NamespaceProtector\Db\BooleanMatchKey;
use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Entry\Entry;

class BooleanMatchKeyTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_evaluate_return_true_if_match(): void
    {
        $entry = new Entry('foo');
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchKey();
        $result = $mach->evaluate($collections, $entry);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_evaluate_return_false_if_match(): void
    {
        $entry = new Entry('foo2');
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchKey();
        $result = $mach->evaluate($collections, $entry);

        $this->assertFalse($result);
    }
}
