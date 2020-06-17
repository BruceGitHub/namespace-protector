<?php
declare(strict_types=1);

namespace Unit\Parser;


use NamespaceProtector\Cache\NullCache;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Config\Config;
use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Scanner\ComposerJson;
use PHPUnit\Framework\TestCase;

class PhpFileParserTest extends TestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $composerJson = new ComposerJson(new FileSystemPath(''));
        $environmentDataLoader = new EnvironmentDataLoader($composerJson);

        $config = new Config(
            '0.1.0',
            new FileSystemPath('..'),
            new FileSystemPath('..'),
            [],
            []
        );

        $nullCache = new NullCache();

        $phpFileParser = new PhpFileParser($config,$environmentDataLoader,$nullCache);

        $this->assertCount(0,$phpFileParser->getListResult()->get());

    }
}
