<?php declare(strict_types=1);

namespace Tests\Unit\OutputDevice;

use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\OutputDevice\ConsoleDevice;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\Result\ResultCollectorReadable;
use NamespaceProtector\Result\ResultProcessedFile;
use NamespaceProtector\Result\ResultProcessorInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleDeviceTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_output_work(): void
    {
        $consoleDevice = new ConsoleDevice(
            new ConsoleOutput()
        );
        \ob_start();

        $rpf = new ResultProcessedFile('FileA');
        $rpf->add(new ErrorResult(99, 'ConflicA', 1));

        $result = $this->prophesize(ResultProcessorInterface::class);

        $result->getProcessedResult()
            ->shouldBeCalled()
            ->willReturn(
                new ResultCollectorReadable(new ResultCollector(
                    [$rpf]
                ))
            );

        $consoleDevice->output($result->reveal());
        $output = \ob_get_clean();

        $this->assertStringContainsString('Processed file: FileA', $output);
        $this->assertStringContainsString('ConflicA', $output);
    }
}
