<?php

declare(strict_types=1);

namespace Tests\All\OutputDevice;

use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;
use Tests\All\AbstractUnitTestCase;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\OutputDevice\ConsoleDevice;
use Symfony\Component\Console\Output\ConsoleOutput;
use NamespaceProtector\Result\ResultCollectedReadable;
use NamespaceProtector\Result\ResultProcessorInterface;
use NamespaceProtector\Result\ResultProcessedMutableFile;

class ConsoleDeviceTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_output_work(): void
    {
        $consoleDevice = new ConsoleDevice(new ConsoleOutput());
        \ob_start();

        $rpf = new ResultProcessedMutableFile(StringVo::fromValue('FileA'));
        $rpf->addConflic(new ErrorResult(
            IntegerVo::fromValue(99),
            StringVo::fromValue('ConflicA'),
            IntegerVo::fromValue(1)
            )
        );

        $result = $this->prophesize(ResultProcessorInterface::class);

        $result->getProcessedResult()
            ->shouldBeCalled()
            ->willReturn(
                new ResultCollectedReadable(new ResultCollected(
                    [$rpf]
                ))
            );

        $consoleDevice->output($result->reveal());
        $output = \ob_get_clean();

        $this->assertStringContainsString('Processed file: FileA', $output);
        $this->assertStringContainsString('ConflicA', $output);
    }
}
