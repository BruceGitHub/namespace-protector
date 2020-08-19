<?php

declare(strict_types=1);

namespace NamespaceProtector;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Psr\SimpleCache\CacheInterface;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Cache\NullCache;
use NamespaceProtector\Parser\Node\NamespaceVisitor;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Cache\SimpleFileCache;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Event\EventDispatcher;
use NamespaceProtector\Event\ListenerProvider;
use NamespaceProtector\Scanner\FileSystemScanner;
use NamespaceProtector\Parser\Node\ProcessUseStatement;
use NamespaceProtector\Parser\Node\Event\FoundUseNamespace;

final class NamespaceProtectorProcessorFactory
{
    private const NAMESPACE_PROTECTOR_CACHE = 'namespace-protector-cache';

    public function create(Config $config): NamespaceProtectorProcessor
    {
        $composerJson = new ComposerJson($config->getPathComposerJson());
        $fileSystem = new FileSystemScanner([$config->getStartPath()]);
        $metaDataLoader = new EnvironmentDataLoader($composerJson);
        $cacheClass = $this->createCacheObject($config);

        $listener = new ListenerProvider();
        $callableUseStatement = new ProcessUseStatement($metaDataLoader, $config);
        $listener->addEventListener(FoundUseNamespace::class, $callableUseStatement);
        $dispatcher = new EventDispatcher($listener);

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();

        $namespaceVisitor = new NamespaceVisitor(
            [
                'preserveOriginalNames' => true,
                'replaceNodes' => false,
            ],
            $dispatcher
        );

        $analyser = new Analyser(
            new PhpFileParser(
                $cacheClass,
                $traverser,
                $namespaceVisitor,
                $parser
            )
        );

        $namespaceProtectorProcessor = new NamespaceProtectorProcessor(
            $composerJson,
            $fileSystem,
            $analyser,
            $metaDataLoader
        );

        return $namespaceProtectorProcessor;
    }

    private function createCacheObject(Config $config): CacheInterface
    {
        if ($config->enabledCache()) {
            $directory = \sys_get_temp_dir() . self::NAMESPACE_PROTECTOR_CACHE;

            return new SimpleFileCache(new FileSystemPath($directory, true));
        }

        return new NullCache();
    }
}
