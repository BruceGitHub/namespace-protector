<?php declare(strict_types=1);

namespace Tests\Unit\Db;

use NamespaceProtector\Entry\Entry;
use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Db\BooleanMatchValue;
use NamespaceProtector\Parser\Node\MatchedResult;
use NamespaceProtector\Parser\Node\EmptyMatchedResult;

class BooleanMatchValueTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_evaluate_return_true_if_match(): void
    {
        $entry = new Entry('bar');
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchValue();
        $result = $mach->evaluate($collections, $entry);

        $this->assertInstanceOf(MatchedResult::class, $result);
    }

    /** @test */
    public function it_evaluate_return_false_if_match(): void
    {
        $entry = new Entry('bar2');
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchValue();
        $result = $mach->evaluate($collections, $entry);

        $this->assertInstanceOf(EmptyMatchedResult::class, $result);
    }
}
