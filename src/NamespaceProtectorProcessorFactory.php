<?php
namespace NamespaceProtector;

use NamespaceProtector\Analyser;
use Psr\SimpleCache\CacheInterface;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Cache\NullCache;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Cache\SimpleFileCache;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\Scanner\FileSystemScanner;
use NamespaceProtector\OutputDevice\ConsoleDevice;
use NamespaceProtector\NamespaceProtectorProcessor;

class NamespaceProtectorProcessorFactory
{
    private const NAMESPACE_PROTECTOR_CACHE = 'namespace-protector-cache';

    public function create(Config $config): NamespaceProtectorProcessor
    {
        $composerJson = new ComposerJson($config->getPathComposerJson());
        $fileSystem = new FileSystemScanner([$config->getStartPath()]);
        $metaDataLoader = new EnvironmentDataLoader($composerJson);
        $cacheClass = $this->createCacheObject($config);
        $analyser = new Analyser(new ConsoleDevice(), new PhpFileParser($config, $metaDataLoader, $cacheClass));

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
            $directory = \sys_get_temp_dir().self::NAMESPACE_PROTECTOR_CACHE;

            return new SimpleFileCache(new FileSystemPath($directory, true));
        }
        
        return new NullCache();
    }
}
