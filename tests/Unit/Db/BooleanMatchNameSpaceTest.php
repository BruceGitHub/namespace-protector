<?php declare(strict_types=1);

namespace Tests\Unit\Db;

use NamespaceProtector\Entry\Entry;
use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Db\BooleanMatchNameSpace;
use NamespaceProtector\Parser\Node\MatchedResult;
use NamespaceProtector\Parser\Node\EmptyMatchedResult;

class BooleanMatchNameSpaceTest extends AbstractUnitTestCase
{
    /**
     * @test
     * @dataProvider publicEntryProvider
    */
    public function it_namespace_inside_another_namespace(array $publicEntry, string $matchMeFromSourceCode, string $expectedClass): void
    {
        $booleanMatch = new BooleanMatchNameSpace();

        $result = $booleanMatch->evaluate($publicEntry, new Entry($matchMeFromSourceCode));

        $this->assertInstanceOf($expectedClass, $result);
    }

    public function publicEntryProvider(): \Generator
    {
        $data = [
            'safe\sprintf',
            'aa\bb\ccA',
        ];

        yield [$data, '\safe', MatchedResult::class];
        yield [$data, '\\safe\\', MatchedResult::class];
        yield [$data, '\\safe\\sprintf', MatchedResult::class];
        yield [$data, '\\safe\\sprintf\\', MatchedResult::class];

        yield [$data, '\\safe\\XXXsprintf\\', EmptyMatchedResult::class];
        yield [$data, '\\safe\\sprintfs\\', EmptyMatchedResult::class];
        yield [$data, '\\aa\\bb\\ccB', EmptyMatchedResult::class];
    }
}
