<?php

namespace Tests\Unit\Db;

use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Db\BooleanMatchValue;
use NamespaceProtector\Entry\Entry;

class BooleanMatchValueTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_evaluate_return_true_if_match(): void
    {
        $entry = new Entry('bar');
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchValue();
        $result = $mach->evaluate($collections, $entry);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_evaluate_return_false_if_match(): void
    {
        $entry = new Entry('bar2');
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchValue();
        $result = $mach->evaluate($collections, $entry);

        $this->assertFalse($result);
    }
}
