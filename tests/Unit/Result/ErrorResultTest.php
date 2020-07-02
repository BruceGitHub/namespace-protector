<?php
namespace Tests\Unit\Result;

use NamespaceProtector\Result\ErrorResult;
use Tests\Unit\AbstractUnitTestCase;

class ErrorResultTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_get_work(): void
    {
        $err = new ErrorResult(111, 'minny', 999);

        $this->assertEquals("\t > ERROR Line: 111 of use minny ", $err->get());
        $this->assertEquals(999, $err->getType());
    }
}
