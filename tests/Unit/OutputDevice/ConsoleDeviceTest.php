<?php declare(strict_types=1);

namespace Tests\Unit\OutputDevice;

use NamespaceProtector\OutputDevice\ConsoleDevice;
use Tests\Unit\AbstractUnitTestCase;

class ConsoleDeviceTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_output_work(): void
    {
        $consoleDevice = new ConsoleDevice();
        \ob_start();
        $consoleDevice->output('hello device');
        $output = \ob_get_clean();

        $this->assertEquals('hello device', $output);
    }
}
