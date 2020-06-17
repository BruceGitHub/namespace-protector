<?php
declare(strict_types=1);

namespace Unit\Result;


use NamespaceProtector\Result\Result;
use NamespaceProtector\Result\ResultCollector;
use PHPUnit\Framework\TestCase;

final class ResultCollectorTest extends TestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $result = new Result('a', 1);

        $resultCollector = new ResultCollector();
        $resultCollector->addResult($result);

        $this->assertCount(1, $resultCollector->get());
        $this->assertEquals($result,$resultCollector->get()[0]);
    }

    /** @test */
    public function it_empty_work(): void
    {
        $result = new Result('a', 1);

        $resultCollector = new ResultCollector();
        $resultCollector->addResult($result);
        $resultCollector->emptyResult();

        $this->assertCount(0, $resultCollector->get());

    }
}
