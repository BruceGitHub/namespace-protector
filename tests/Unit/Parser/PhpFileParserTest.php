<?php

declare(strict_types=1);

namespace Unit\Parser;

use NamespaceProtector\Cache\NullCache;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Scanner\ComposerJson;
use Tests\Unit\AbstractUnitTestCase;

class PhpFileParserTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url() . '/json';
        $file = new FileSystemPath($fsPath . '/namespace-protector-config.json');

        $composerJson = new ComposerJson(new FileSystemPath($fsPath));
        $environmentDataLoader = new EnvironmentDataLoader($composerJson);
        $config = Config::loadFromFile($file);

        //act
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        $this->assertCount(0, $phpFileParser->getListResult()->get());
    }

    /** @test */
    public function it_parse_work(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-mod-public.json', 'json', 'json')
            ->addFile('first.php', 'php', 'files')
            ->buildFileSystemUrl();

        $pathStubClass = new FileSystemPath($fileSystem . '/files/first.php');
        $environmentDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile(new FileSystemPath($fileSystem . '/json/namespace-protector-config-mod-public.json'));
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        //act
        $phpFileParser->parseFile($pathStubClass);

        $rsCollector = $phpFileParser->getListResult();

        $this->assertEquals([], $rsCollector->get());
    }

    /** @test */
    public function it_parse_return_empty_result_when_no_violation(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url() . '/json';
        $files =  $this->getVirtualFileSystem()->url() . '/files';
        $file = new FileSystemPath($fsPath . '/namespace-protector-config.json');

        $composerJson = new ComposerJson(new FileSystemPath($fsPath));
        $composerJson->load();

        $environmentDataLoader = new EnvironmentDataLoader($composerJson);
        $environmentDataLoader->load();
        $config = Config::loadFromFile($file);

        //act
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        $phpFileParser->parseFile(new FileSystemPath($files . '/no_violation.php'));
        $rsCollector = $phpFileParser->getListResult();

        $this->assertEquals([], $rsCollector->get());
    }

    /** @test */
    public function it_trigger_violation_when_not_entries_with_mod_vendor_private(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-mod-private.json', 'json', 'json')
            ->addFile('FileThatUsePrivateNamespace.php', 'php', 'files')
            ->buildFileSystemUrl();

        $environmentDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile(new FileSystemPath($fileSystem . '/json/namespace-protector-config-mod-private.json'));
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        //act
        $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUsePrivateNamespace.php'));

        $rsCollector = $phpFileParser->getListResult();
        $this->assertCount(4, $rsCollector->get());
        $this->assertStringContainsString('files/FileThatUsePrivateNamespace.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 5 of use org\bovigo\vfs\vfsStream', $rsCollector->get()[1]->get());
        $this->assertStringContainsString('> ERROR Line: 11 of use \org\bovigo\vfs\vfsStream', $rsCollector->get()[2]->get());
        $this->assertStringContainsString('> ERROR Line: 12 of use \xxxx\vsf\vfsStream', $rsCollector->get()[3]->get());
    }

    /** @test */
    public function it_no_violations_with_mod_public(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-mod-public.json', 'json', 'json')
            ->addFile('ClassPsr4Composer.php', 'php', 'files')
            ->buildFileSystemUrl();

        $configJson = new FileSystemPath($fileSystem . '/json/namespace-protector-config-mod-public.json');
        $pathStubClass = new FileSystemPath($fileSystem . '/files/ClassPsr4Composer.php');

        $environmentDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile($configJson);
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        //act
        $phpFileParser->parseFile($pathStubClass);

        $rsCollector = $phpFileParser->getListResult();
        $this->assertCount(0, $rsCollector->get());
    }

    /** @test */
    public function it_violations_with_private_entries_with_mod_public(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFileWithCallable(
                'namespace-protector-config-mod-public.json',
                'json',
                'json',
                function ($directoryReal, $pathFile) {
                    return $this->helperEditChangesEntries($directoryReal, $pathFile, ['another\ns\vendor']);
                }
            )
            ->addFile('ClassPsr4Composer.php', 'php', 'files')
            ->buildFileSystem();

        $fileConfigJson =  $fileSystem->url() . '/json/namespace-protector-config-mod-public.json';
        $pathStubClass =  $fileSystem->url() . '/files/ClassPsr4Composer.php';

        $environmentDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile(new FileSystemPath($fileConfigJson));
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        //act
        $phpFileParser->parseFile(new FileSystemPath($pathStubClass));

        $rsCollector = $phpFileParser->getListResult();

        $this->assertCount(3, $rsCollector->get());
        $this->assertStringContainsString('Process file: vfs://root/files/ClassPsr4Composer.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 6 of use another\ns\vendor', $rsCollector->get()[1]->get());
        $this->assertStringContainsString('> ERROR Line: 10 of use \another\ns\vendor', $rsCollector->get()[2]->get());
    }

    /** @test */
    public function it_violations_with_private_and_public_entries_equals_with_mod_public(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFileWithCallable(
                'namespace-protector-config-mod-public.json',
                'json',
                'json',
                function ($directoryReal, $pathFile) {
                    return $this->helperEditChangesEntries(
                        $directoryReal,
                        $pathFile,
                        ['another\ns\vendor'],
                        ['another\ns\vendor']
                    );
                }
            )
            ->addFile('ClassPsr4Composer.php', 'php', 'files')
            ->buildFileSystem();

        $fileConfigJson =  $fileSystem->url() . '/json/namespace-protector-config-mod-public.json';
        $pathStubClass =  $fileSystem->url() . '/files/ClassPsr4Composer.php';

        $environmentDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile(new FileSystemPath($fileConfigJson));
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        //act
        $phpFileParser->parseFile(new FileSystemPath($pathStubClass));

        $rsCollector = $phpFileParser->getListResult();

        $this->assertCount(3, $rsCollector->get());
        $this->assertStringContainsString('Process file: vfs://root/files/ClassPsr4Composer.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 6 of use another\ns\vendor', $rsCollector->get()[1]->get());
        $this->assertStringContainsString('> ERROR Line: 10 of use \another\ns\vendor', $rsCollector->get()[2]->get());
    }

    private function helperEditChangesEntries($directoryReal, $pathFile, array $nsPrivate = [], array $nsPublic = [])
    {
        $data = \file_get_contents(__DIR__ . '/../../Stub/' . $directoryReal . '/' . $pathFile);
        $data = \json_decode($data, true);
        $data['private-entries'] = $nsPrivate;
        $data['public-entries'] = $nsPublic;

        return \json_encode($data);
        return $data;
    }


    /** @test */
    public function it_violations_with_mod_vendor_private(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-mod-private.json', 'json', 'json')
            ->addFile('ClassPsr4Composer.php', 'php', 'files')
            ->buildFileSystem();

        $fileConfigJson =  $fileSystem->url() . '/json/namespace-protector-config-mod-private.json';
        $pathStubClass =  $fileSystem->url() . '/files/ClassPsr4Composer.php';

        $environmentDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile(new FileSystemPath($fileConfigJson));
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        //act
        $phpFileParser->parseFile(new FileSystemPath($pathStubClass));

        $rsCollector = $phpFileParser->getListResult();

        $this->assertCount(4, $rsCollector->get());
        $this->assertStringContainsString('Process file: vfs://root/files/ClassPsr4Composer.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 5 of use my\ns\psr4', $rsCollector->get()[1]->get());
        $this->assertStringContainsString('> ERROR Line: 6 of use another\ns\vendor', $rsCollector->get()[2]->get());
        $this->assertStringContainsString('> ERROR Line: 10 of use \another\ns\vendor', $rsCollector->get()[3]->get());
    }

    /** @test */
    public function it_no_violation_when_public_entries_configured_with_mod_vendor_private(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFileWithCallable(
                'namespace-protector-config-mod-private.json',
                'json',
                'json',
                function ($directoryReal, $pathFile) {
                    return $this->helperEditChangesEntries(
                        $directoryReal,
                        $pathFile,
                        [],
                        ['inpublic\entries\ns']
                    );
                }
            )
            ->addFile('FileThatUsePublicEntry.php', 'php', 'files')
            ->buildFileSystemUrl();

        $environmentDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile(new FileSystemPath($fileSystem . '/json/namespace-protector-config-mod-private.json'));
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        //act
        $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUsePublicEntry.php'));

        $rsCollector = $phpFileParser->getListResult();
        $this->assertCount(0, $rsCollector->get());
    }

    /** @test */
    public function it_violation_when_entry_and_target_are_different_case_mod_vendor_private(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFileWithCallable(
                'namespace-protector-config-mod-private.json',
                'json',
                'json',
                function ($directoryReal, $pathFile) {
                    return $this->helperEditChangesEntries(
                        $directoryReal,
                        $pathFile,
                        ['InPublic\ENTRIES\Ns'],
                        []
                    );
                }
            )
            ->addFile('FileThatUseLowerCaseNaspace.php', 'php', 'files')
            ->buildFileSystemUrl();

        $environmentDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile(new FileSystemPath($fileSystem . '/json/namespace-protector-config-mod-private.json'));
        $phpFileParser = new PhpFileParser($config, $environmentDataLoader, new NullCache());

        //act
        $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUseLowerCaseNaspace.php'));

        $rsCollector = $phpFileParser->getListResult();
        $this->assertCount(4, $rsCollector->get());
        $this->assertStringContainsString('Process file: vfs://root/files/FileThatUseLowerCaseNaspace.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 5 of use inpublic\entries\ns', $rsCollector->get()[1]->get());
        $this->assertStringContainsString('> ERROR Line: 11 of use \inpublic\entries\NS', $rsCollector->get()[2]->get());
        $this->assertStringContainsString('> ERROR Line: 12 of use \inpublic\entries\ns', $rsCollector->get()[3]->get());
    }

    private function getEnvironmentMock(
        array $constans = [],
        array $baseFunctions = [],
        array $baseClasses = [],
        array $composerNs = [],
        array $baseInterfaces = []
    ) {
        $environmentDataLoader = $this->prophesize(EnvironmentDataLoaderInterface::class);
        $environmentDataLoader->getCollectBaseConstants()
            ->willReturn(new DbKeyValue());

        $environmentDataLoader->getCollectBaseFunctions()
            ->willReturn(new DbKeyValue());

        $environmentDataLoader->getCollectBaseClasses()
            ->willReturn(new DbKeyValue());

        $environmentDataLoader->getCollectComposerNamespace()
            ->willReturn(new DbKeyValue($composerNs));

        $environmentDataLoader->getCollectBaseInterfaces()
            ->willReturn(new DbKeyValue());

        $environmentDataLoader = $environmentDataLoader->reveal();


        return $environmentDataLoader;
    }
}
