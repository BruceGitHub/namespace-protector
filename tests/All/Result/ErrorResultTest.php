<?php declare(strict_types=1);

namespace Tests\All\Result;

use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Result\ErrorResult;
use Tests\All\AbstractUnitTestCase;

class ErrorResultTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_get_work(): void
    {
        $err = new ErrorResult(IntegerVo::fromValue(111), StringVo::fromValue('minny'), IntegerVo::fromValue(999));

        $this->assertEquals('minny', $err->get()->toValue());
        $this->assertEquals(IntegerVo::fromValue(999), $err->getType());
    }
}
