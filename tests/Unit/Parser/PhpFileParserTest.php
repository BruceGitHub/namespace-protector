<?php

declare(strict_types=1);

namespace Unit\Parser;

use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\Cache\NullCache;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Event\EventDispatcher;
use NamespaceProtector\Event\ListenerProvider;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\Node\ProcessUseStatement;
use NamespaceProtector\Parser\Node\Event\FoundUseNamespace;

class PhpFileParserTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url() . '/json';
        $file = $fsPath . '/namespace-protector-config.json';

        $phpFileParser = $this->createPhpFileParser($file);

        $this->assertCount(0, $phpFileParser->getListResult()->get());
    }

    /** @test */
    public function it_parse_work(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-mod-public.json', 'json', 'json')
            ->addFile('First.php', 'php', 'files')
            ->buildFileSystemUrl();

        $pathStubClass = new FileSystemPath($fileSystem . '/files/First.php');
        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-public.json');

        //act
        $phpFileParser->parseFile($pathStubClass);

        $rsCollector = $phpFileParser->getListResult();
        $this->assertEquals([], $rsCollector->get());
    }

    /** @test */
    public function it_parse_return_empty_result_when_no_violation(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url() . '/json';
        $files = $this->getVirtualFileSystem()->url() . '/files';

        $phpFileParser = $this->createPhpFileParser($fsPath . '/namespace-protector-config.json');

        $phpFileParser->parseFile(new FileSystemPath($files . '/no_violation.php'));
        $rsCollector = $phpFileParser->getListResult();

        $this->assertEquals([], $rsCollector->get());
    }

    /** @test */
    public function it_parse_with_public_class(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-with-pub-class.json', 'json', 'json')
            ->addFile('First.php', 'php', 'files')
            ->addFile('Second.php', 'php', 'files')
            ->buildFileSystemUrl();

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-with-pub-class.json');

        $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/Second.php'));
        $rsCollector = $phpFileParser->getListResult();

        $this->assertEquals([], $rsCollector->get());
    }

    /** @test */
    public function it_parse_with_public_class_with_name_inside_another_class_name(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
                ->addFileWithCallable(
                    'namespace-protector-config-with-class.json',
                    'json',
                    'json',
                    function ($directoryReal, $pathFile) {
                        return $this->helperEditChangesEntries(
                            $directoryReal,
                            $pathFile,
                            [],
                            [
                                'Personal\\First',
                                'Personal\\Private',
                            ]
                        );
                    }
                )
                ->addFile('First.php', 'php', 'files')
                ->addFile('Second.php', 'php', 'files') //use Privates
                ->buildFileSystemUrl();

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-with-class.json');

        $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/Second.php'));
        $rsCollector = $phpFileParser->getListResult();

        $this->assertCount(2, $rsCollector->get());
        $this->assertStringContainsString('Process file: vfs://root/files/Second.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 6 of use Personal\Privates', $rsCollector->get()[1]->get());
    }

    /** @test */
    public function it_parse_with_public_class_with_name_inside_another_namespace(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
                ->addFileWithCallable(
                    'namespace-protector-config-with-class.json',
                    'json',
                    'json',
                    function ($directoryReal, $pathFile) {
                        return $this->helperEditChangesEntries(
                            $directoryReal,
                            $pathFile,
                            [],
                            [
                                'Personal\\First',
                                'Personal\Privates\PrivatesB',
                            ]
                        );
                    }
                )
                ->addFile('First.php', 'php', 'files')
                ->addFile('UsePublicNsAndOnePrivateClass.php', 'php', 'files') //use Privates
                ->buildFileSystemUrl();

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-with-class.json');

        $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/UsePublicNsAndOnePrivateClass.php'));
        $rsCollector = $phpFileParser->getListResult();

        $this->assertCount(2, $rsCollector->get());
        $this->assertStringContainsString('Process file: vfs://root/files/UsePublicNsAndOnePrivateClass.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 8 of use Personal\Privates', $rsCollector->get()[1]->get());
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

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-private.json');

        //act
        $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUseLowerCaseNaspace.php'));

        $rsCollector = $phpFileParser->getListResult();
        $this->assertCount(4, $rsCollector->get());
        $this->assertStringContainsString('Process file: vfs://root/files/FileThatUseLowerCaseNaspace.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 5 of use inpublic\entries\ns', $rsCollector->get()[1]->get());
        $this->assertStringContainsString('> ERROR Line: 11 of use \inpublic\entries\NS', $rsCollector->get()[2]->get());
        $this->assertStringContainsString('> ERROR Line: 12 of use \inpublic\entries\ns', $rsCollector->get()[3]->get());
    }

    /** @test */
    public function it_trigger_violation_when_not_entries_with_mod_vendor_private(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-mod-private.json', 'json', 'json')
            ->addFile('FileThatUsePrivateNamespace.php', 'php', 'files')
            ->buildFileSystemUrl();

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-private.json');

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

        $pathStubClass = new FileSystemPath($fileSystem . '/files/ClassPsr4Composer.php');
        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-public.json');

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
            ->buildFileSystemUrl();

        $pathStubClass = $fileSystem . '/files/ClassPsr4Composer.php';
        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-public.json');

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
            ->buildFileSystemUrl();

        $pathStubClass = $fileSystem . '/files/ClassPsr4Composer.php';
        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-public.json');

        //act
        $phpFileParser->parseFile(new FileSystemPath($pathStubClass));

        $rsCollector = $phpFileParser->getListResult();

        $this->assertCount(3, $rsCollector->get());
        $this->assertStringContainsString('Process file: vfs://root/files/ClassPsr4Composer.php', $rsCollector->get()[0]->get());
        $this->assertStringContainsString('> ERROR Line: 6 of use another\ns\vendor', $rsCollector->get()[1]->get());
        $this->assertStringContainsString('> ERROR Line: 10 of use \another\ns\vendor', $rsCollector->get()[2]->get());
    }

    /** @test */
    public function it_violations_with_mod_vendor_private(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-mod-private.json', 'json', 'json')
            ->addFile('ClassPsr4Composer.php', 'php', 'files')
            ->buildFileSystemUrl();

        $pathStubClass = $fileSystem . '/files/ClassPsr4Composer.php';
        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-private.json');

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

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-private.json');

        //act
        $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUsePublicEntry.php'));

        $rsCollector = $phpFileParser->getListResult();
        $this->assertCount(0, $rsCollector->get());
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

    private function createPhpFileParser(string $pathConfig): PhpFileParser
    {
        $metaDataLoader = $this->getEnvironmentMock();
        $config = Config::loadFromFile(new FileSystemPath($pathConfig));
        $listener = new ListenerProvider();
        $callableUseStatement = new ProcessUseStatement($metaDataLoader, $config);
        $listener->addEventListener(FoundUseNamespace::class, $callableUseStatement);
        $dispatcher = new EventDispatcher($listener);

        return new PhpFileParser(new NullCache(), $dispatcher);
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
}
