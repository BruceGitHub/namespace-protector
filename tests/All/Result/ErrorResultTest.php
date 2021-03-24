<?php declare(strict_types=1);

namespace Tests\All\Result;

use NamespaceProtector\Result\ErrorResult;
use Tests\All\AbstractUnitTestCase;

class ErrorResultTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_get_work(): void
    {
        $err = new ErrorResult(111, 'minny', 999);

        $this->assertEquals('minny', $err->get());
        $this->assertEquals(999, $err->getType());
    }
}
