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
    public function it_namespace_inside_another_namespace(array $configuredEntry, string $tokenFromSourceCode, string $expectedClass): void
    {
        $booleanMatch = new BooleanMatchNameSpace();

        $result = $booleanMatch->evaluate($configuredEntry, new Entry($tokenFromSourceCode));

        $this->assertInstanceOf($expectedClass, $result);
    }

    public function publicEntryProvider(): \Generator
    {
        $configuredEntry = ['safe\\', '\\safe', '\\safe\\'];
        yield [$configuredEntry, '\safe', MatchedResult::class];

        $configuredEntry = ['safe\\aa', '\\safe\\aa', '\\safe\\aa'];
        yield [$configuredEntry, '\safe\\aa', MatchedResult::class];

        $configuredEntry = ['safe\sprintf'];
        yield [$configuredEntry, '\safe', EmptyMatchedResult::class];

        $configuredEntry = ['safe\loooong'];
        yield [$configuredEntry, '\\safe\\', EmptyMatchedResult::class];

        $configuredEntry = ['\\safe\\loooong\\'];
        yield [$configuredEntry, '\\safe\\loooong', MatchedResult::class];

        $configuredEntry = ['PhpParser'];
        yield [$configuredEntry, 'PhpParser\\NodeTraverser', MatchedResult::class]; //MatchedResult
    }
}
