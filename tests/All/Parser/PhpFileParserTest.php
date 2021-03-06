<?php

declare(strict_types=1);

namespace Unit\Parser;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Tests\All\AbstractUnitTestCase;
use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\Cache\NullCache;
use NamespaceProtector\Config\ConfigMaker;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Event\EventDispatcher;
use NamespaceProtector\Event\ListenerProvider;
use NamespaceProtector\Parser\Node\NamespaceVisitor;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\Node\ProcessUseStatement;
use NamespaceProtector\Result\Factory\CollectedFactory;
use NamespaceProtector\Parser\Node\Event\FoundUseNamespace;
use NamespaceProtector\Result\Factory\ErrorCollectionFactory;

class PhpFileParserTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url() . '/json';
        $file = $fsPath . '/namespace-protector-config.json';

        $phpFileParser = $this->createPhpFileParser($file);

        $result = $phpFileParser->parseFile(new FileSystemPath($file));

        $this->assertCount(0, $result->getResultCollectionReadable());
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
        $result = $phpFileParser->parseFile($pathStubClass);

        $this->assertCount(0, $result->getResultCollectionReadable());
    }

    /** @test */
    public function it_parse_return_empty_result_when_no_violation(): void
    {
        $fsPath = $this->getVirtualFileSystem()->url() . '/json';
        $files = $this->getVirtualFileSystem()->url() . '/files';

        $phpFileParser = $this->createPhpFileParser($fsPath . '/namespace-protector-config.json');

        $result = $phpFileParser->parseFile(new FileSystemPath($files . '/no_violation.php'));

        $this->assertCount(0, $result->getResultCollectionReadable());
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

        $result = $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/Second.php'));

        $this->assertCount(0, $result->getResultCollectionReadable());
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
            ->addFile('Second.php', 'php', 'files') //use Privates no conflicts
            ->buildFileSystemUrl();

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-with-class.json');

        $result = $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/Second.php'));

        $this->assertEquals(0, $result->getResultCollectionReadable()->count());
        $this->assertContainsProcessFile('vfs://root/files/Second.php', $result->getResultCollectionReadable());
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
                            'Personal\Privates\PrivatesA',
                        ]
                    );
                }
            )
            ->addFile('First.php', 'php', 'files')
            ->addFile('UsePublicNsAndOnePrivateClass.php', 'php', 'files') //use Privates
            ->buildFileSystemUrl();

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-with-class.json');

        $result = $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/UsePublicNsAndOnePrivateClass.php'));

        $this->assertEquals(1, $result->getResultCollectionReadable()->count());
        $this->assertContainsProcessFile('vfs://root/files/UsePublicNsAndOnePrivateClass.php', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('Personal\Privates\PrivatesB', $result->getResultCollectionReadable());
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
        $result = $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUseLowerCaseNaspace.php'));

        $this->assertCount(1, $result->getResultCollectionReadable());
        $this->assertContainsProcessFile('vfs://root/files/FileThatUseLowerCaseNaspace.php', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('inpublic\entries\ns', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('\inpublic\entries\NS', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('\inpublic\entries\ns', $result->getResultCollectionReadable());
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
        $result = $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUsePrivateNamespace.php'));

        $this->assertCount(1, $result->getResultCollectionReadable());
        $this->assertContainsProcessFile('files/FileThatUsePrivateNamespace.php', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('org\bovigo\vfs\vfsStream', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('\org\bovigo\vfs\vfsStream', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('\xxxx\vsf\vfsStream', $result->getResultCollectionReadable());
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
        $result = $phpFileParser->parseFile($pathStubClass);

        $this->assertCount(0, $result->getResultCollectionReadable());
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
        $result = $phpFileParser->parseFile(new FileSystemPath($pathStubClass));

        $this->assertCount(1, $result->getResultCollectionReadable());
        $this->assertContainsProcessFile('vfs://root/files/ClassPsr4Composer.php', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('another\ns\vendor', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('\another\ns\vendor', $result->getResultCollectionReadable());
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
        $result = $phpFileParser->parseFile(new FileSystemPath($pathStubClass));

        $this->assertCount(1, $result->getResultCollectionReadable());
        $this->assertContainsProcessFile('vfs://root/files/ClassPsr4Composer.php', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('another\ns\vendor', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('\another\ns\vendor', $result->getResultCollectionReadable());
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
        $result = $phpFileParser->parseFile(new FileSystemPath($pathStubClass));

        $this->assertCount(1, $result->getResultCollectionReadable());
        $this->assertContainsProcessFile('vfs://root/files/ClassPsr4Composer.php', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('my\ns\psr4', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('another\ns\vendor', $result->getResultCollectionReadable());
        $this->assertContainsConflicts('\another\ns\vendor', $result->getResultCollectionReadable());
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
        $result = $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUsePublicEntry.php'));

        $this->assertCount(0, $result->getResultCollectionReadable());
    }

    /** @test */
    public function it_no_violations_when_script_uses_root_namespace(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-mod-private.json', 'json', 'json')
            ->addFile('FileThatUseRootNamespace.php', 'php', 'files')
            ->buildFileSystemUrl();

        $phpFileParser = $this->createPhpFileParser($fileSystem . '/json/namespace-protector-config-mod-private.json');

        //act
        $result = $phpFileParser->parseFile(new FileSystemPath($fileSystem . '/files/FileThatUseRootNamespace.php'));

        $this->assertCount(0, $result->getResultCollectionReadable());
        $this->assertAbsentsConflicts($result->getResultCollectionReadable());
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

        $configMaker = new ConfigMaker();
        $config = $configMaker->createFromFile(new FileSystemPath($pathConfig));
        $listener = new ListenerProvider();
        $cache = new NullCache();
        $callableUseStatement = new ProcessUseStatement($metaDataLoader, $config, $cache);
        $listener->addEventListener(FoundUseNamespace::class, $callableUseStatement);
        $dispatcher = new EventDispatcher($listener);

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();
        $collectionFactory = new CollectedFactory();
        $errorCollectionFactory = new ErrorCollectionFactory();

        $namespaceVisitor = new NamespaceVisitor(
            [
                'preserveOriginalNames' => true,
                'replaceNodes' => false,
            ],
            $dispatcher,
            $errorCollectionFactory
        );

        return new PhpFileParser(new NullCache(), $traverser, $namespaceVisitor, $parser, $collectionFactory);
    }

    private function helperEditChangesEntries($directoryReal, $pathFile, array $nsPrivate = [], array $nsPublic = [])
    {
        $data = \file_get_contents(__DIR__ . '/../../Stub/' . $directoryReal . '/' . $pathFile);
        $data = \json_decode($data, true);
        $data['private-entries'] = $nsPrivate;
        $data['public-entries'] = $nsPublic;

        return \json_encode($data);
    }
}
