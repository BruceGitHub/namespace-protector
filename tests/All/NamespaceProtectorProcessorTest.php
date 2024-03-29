<?php declare(strict_types=1);

namespace Tests\All;

use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Config\ConfigMaker;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\OutputDevice\ConsoleDevice;
use Symfony\Component\Console\Output\ConsoleOutput;
use NamespaceProtector\NamespaceProtectorProcessorFactory;

class NamespaceProtectorProcessorTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_process_work(): void
    {
        $configMaker = new ConfigMaker();
        $config = $configMaker->createFromFile(new FileSystemPath(StringVo::fromValue('tests/Stub/targetProject/json/namespace-protector-config.json')));
        $factory = new NamespaceProtectorProcessorFactory();
        $namespaceProtectorProcessor = $factory->create($config);
        $namespaceProtectorProcessor->load();

        $result = $namespaceProtectorProcessor->process();
        $totalOutput = '';
        $console = new ConsoleDevice(new ConsoleOutput());

        \ob_start();
        $console->output($result);
        $totalOutput = \ob_get_clean();

        $this->assertStringContainsString('Processed file: ./tests/Stub/targetProject/src/Second.php', $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $totalOutput);

        $this->assertStringContainsString('Processed file: ./tests/Stub/targetProject/src/Foo.php', $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo", $totalOutput);

        $this->assertStringContainsString('Processed file: ./tests/Stub/targetProject/src/Bar.php', $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $totalOutput);

        $this->assertStringContainsString('Processed file: ./tests/Stub/targetProject/src/First.php', $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $totalOutput);
    }
}
