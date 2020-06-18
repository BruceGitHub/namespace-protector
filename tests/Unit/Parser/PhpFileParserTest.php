<?php
declare(strict_types=1);

namespace Unit\Parser;

use NamespaceProtector\Cache\NullCache;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Config\Config;
use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Scanner\ComposerJson;
use Tests\Unit\AbstractUnitTestCase;

class PhpFileParserTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url().'/json';
        $file = new FileSystemPath($fsPath.'/namespace-protector-config.json');

        $composerJson = new ComposerJson(new FileSystemPath($fsPath));
        $environmentDataLoader = new EnvironmentDataLoader($composerJson);

        $config = Config::loadFromFile($file);
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        $this->assertCount(0, $phpFileParser->getListResult()->get());
    }

    /** @test */
    public function it_parse_work(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url().'/json';
        $files =  $this->getVirtualFileSystem()->url().'/files';
        $file = new FileSystemPath($fsPath.'/namespace-protector-config.json');

        $composerJson = new ComposerJson(new FileSystemPath($fsPath));
        $composerJson->load();

        $environmentDataLoader = new EnvironmentDataLoader($composerJson);
        $environmentDataLoader->load();

        $config = Config::loadFromFile($file);
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        $phpFileParser->parseFile(new FileSystemPath($files.'/first.php'));
        $rsCollector = $phpFileParser->getListResult();

        $this->assertEquals("Process file: vfs://root/files/first.php\n", $rsCollector->get()[0]->get());
    }

    /** @test */
    public function it_parse_return_no_result_when_no_violation(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url().'/json';
        $files =  $this->getVirtualFileSystem()->url().'/files';
        $file = new FileSystemPath($fsPath.'/namespace-protector-config.json');

        $composerJson = new ComposerJson(new FileSystemPath($fsPath));
        $composerJson->load();

        $environmentDataLoader = new EnvironmentDataLoader($composerJson);
        $environmentDataLoader->load();

        $config = Config::loadFromFile($file);
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        $phpFileParser->parseFile(new FileSystemPath($files.'/no_violation.php'));
        $rsCollector = $phpFileParser->getListResult();

        $this->assertEquals([], $rsCollector->get());
    }
}
