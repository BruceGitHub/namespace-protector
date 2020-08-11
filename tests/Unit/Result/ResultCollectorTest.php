<?php

declare(strict_types=1);

namespace Unit\Result;

use NamespaceProtector\Result\Result;
use NamespaceProtector\Result\ResultCollected;
use PHPUnit\Framework\TestCase;

final class ResultCollectorTest extends TestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $result = new Result('a', 1);

        $resultCollector = new ResultCollected();
        $resultCollector->addResult($result);

        $this->assertCount(1, $resultCollector->getIterator());
        $this->assertEquals($result, $resultCollector->getIterator()->current());
    }

    /** @test */
    public function it_empty_work(): void
    {
        $result = new Result('a', 1);

        $resultCollector = new ResultCollected();
        $resultCollector->addResult($result);
        $resultCollector->emptyResult();

        $this->assertEquals(0, $resultCollector->count());
    }
}
