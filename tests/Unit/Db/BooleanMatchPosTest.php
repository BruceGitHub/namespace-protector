<?php

namespace Tests\Unit\Db;

use NamespaceProtector\Db\BooleanMatchPos;
use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Entry\Entry;

class BooleanMatchPosTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_evaluate_return_true_if_match(): void
    {
        $entry = new Entry('fooBar');
        $collections = ['fooBar' => 'bar'];

        $mach = new BooleanMatchPos();
        $result = $mach->evaluate($collections, $entry);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_evaluate_return_false_if_match(): void
    {
        $entry = new Entry('barFoo');
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchPos();
        $result = $mach->evaluate($collections, $entry);

        $this->assertFalse($result);
    }
}
