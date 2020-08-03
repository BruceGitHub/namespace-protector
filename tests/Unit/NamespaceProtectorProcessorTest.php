<?php declare(strict_types=1);

namespace Tests\Unit;

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

        $result = $namespaceProtectorProcessor->process();
        $totalOutput = '';
        foreach ($result->getResultCollectionReadable()->get() AS $item) {
            $totalOutput .= $item->get();
        }

        $this->assertStringContainsString('Process file: ./tests/Stub/targetProject/src/Second.php', $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $totalOutput);

        $this->assertStringContainsString('Process file: ./tests/Stub/targetProject/src/Foo.php', $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo", $totalOutput);

        $this->assertStringContainsString('Process file: ./tests/Stub/targetProject/src/Bar.php', $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $totalOutput);

        $this->assertStringContainsString('Process file: ./tests/Stub/targetProject/src/First.php', $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $totalOutput);
        $this->assertStringContainsString("\t > ERROR Line: 11 of use \Some", $totalOutput);
    }
}
