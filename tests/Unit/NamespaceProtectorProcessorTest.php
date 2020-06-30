<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\NamespaceProtectorProcessorFactory;

class NamespaceProtectorProcessorTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_process_work(): void
    {
        $config = Config::loadFromFile(new FileSystemPath('tests/Stub/targetProject/json/namespace-protector-config.json'));
        $factory = new NamespaceProtectorProcessorFactory();
        $namespaceProtectorProcessor = $factory->create($config);
        $namespaceProtectorProcessor->load();

        \ob_start();
        $namespaceProtectorProcessor->process();
        $resultOutput = \ob_get_clean();
        $this->assertEquals(5, $namespaceProtectorProcessor->getCountErrors());

        $this->assertStringContainsString("Process file: ./tests/Stub/targetProject/src/Second.php\n", $resultOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream\n", $resultOutput);

        $this->assertStringContainsString("Process file: ./tests/Stub/targetProject/src/Foo.php\n", $resultOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream\n", $resultOutput);

        $this->assertStringContainsString("Process file: ./tests/Stub/targetProject/src/Bar.php\n", $resultOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream\n", $resultOutput);

        $this->assertStringContainsString("Process file: ./tests/Stub/targetProject/src/First.php\n", $resultOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream\n", $resultOutput);
        $this->assertStringContainsString("\t > ERROR Line: 11 of use \Some\n", $resultOutput);
    }
}
