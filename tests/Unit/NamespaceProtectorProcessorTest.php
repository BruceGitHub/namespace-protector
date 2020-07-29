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

        $items = $result->getResultCollectionReadable()->get();
        $this->assertStringContainsString('Process file: ./tests/Stub/targetProject/src/Second.php', $items[0]->get());
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $items[1]->get());

        $this->assertStringContainsString('Process file: ./tests/Stub/targetProject/src/Foo.php', $items[2]->get());
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo", $items[3]->get());

        $this->assertStringContainsString('Process file: ./tests/Stub/targetProject/src/Bar.php', $items[4]->get());
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $items[5]->get());

        $this->assertStringContainsString('Process file: ./tests/Stub/targetProject/src/First.php', $items[6]->get());
        $this->assertStringContainsString("\t > ERROR Line: 5 of use dummy\bovigo\\vfs\\vfsStream", $items[7]->get());
        $this->assertStringContainsString("\t > ERROR Line: 11 of use \Some", $items[8]->get());
    }
}
