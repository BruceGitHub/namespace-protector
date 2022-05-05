<?php
declare(strict_types=1);

namespace Tests\All\Result;

use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Result\Result;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $result = new Result(StringVo::fromValue('value'), IntegerVo::fromValue(1));

        $this->assertEquals($result->get()->toValue(), 'value');
        $this->assertEquals($result->getType(), IntegerVo::fromValue(1));
    }
}
