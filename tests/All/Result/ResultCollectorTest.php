<?php

declare(strict_types=1);

namespace Tests\All\Result;

use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Result\Result;
use NamespaceProtector\Result\ResultCollected;
use PHPUnit\Framework\TestCase;

final class ResultCollectorTest extends TestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $result = new Result(StringVo::fromValue('a'), IntegerVo::fromValue(1));

        $resultCollector = new ResultCollected();
        $resultCollector->addResult($result);

        $this->assertCount(1, $resultCollector->getIterator());
        $this->assertEquals($result, $resultCollector->getIterator()->current());
    }

    /** @test */
    public function it_empty_work(): void
    {
        $result = new Result(StringVo::fromValue('a'), IntegerVo::fromValue(1));

        $resultCollector = new ResultCollected();
        $resultCollector->addResult($result);
        $resultCollector->emptyResult();

        $this->assertEquals(0, $resultCollector->count());
    }
}
