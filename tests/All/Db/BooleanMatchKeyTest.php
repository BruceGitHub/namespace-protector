<?php declare(strict_types=1);

namespace Tests\All\Db;

use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Entry\Entry;
use Tests\All\AbstractUnitTestCase;
use NamespaceProtector\Db\BooleanMatchKey;
use NamespaceProtector\Parser\Node\MatchedResult;
use NamespaceProtector\Parser\Node\EmptyMatchedResult;

class BooleanMatchKeyTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_evaluate_return_true_if_match(): void
    {
        $entry = new Entry(StringVo::fromValue('foo'));
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchKey();
        $result = $mach->evaluate($collections, $entry);

        $this->assertInstanceOf(MatchedResult::class, $result);
    }

    /** @test */
    public function it_evaluate_return_false_if_match(): void
    {
        $entry = new Entry(StringVo::fromValue('foo2'));
        $collections = ['foo' => 'bar'];

        $mach = new BooleanMatchKey();
        $result = $mach->evaluate($collections, $entry);

        $this->assertInstanceOf(EmptyMatchedResult::class, $result);
    }
}
