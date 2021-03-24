<?php
declare(strict_types=1);

namespace Unit\Result;

use NamespaceProtector\Result\Result;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $result = new Result('value', 1);

        $this->assertEquals($result->get(), 'value');
        $this->assertEquals($result->getType(), 1);
    }
}
