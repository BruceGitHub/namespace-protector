<?php declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Config\Config;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Scanner\FileSystemScanner;
use NamespaceProtector\Result\ResultParserInterface;
use NamespaceProtector\Result\ResultParserNamespaceValidate;

final class NamespaceProtectorProcessor
{
    private const NAMESPACE_PROTECTOR_CACHE = 'namespace-protector-cache';

    /** @var Config */
    private $config;

    /** @var ComposerJson */
    private $composerJson;

    /** @var FileSystemScanner */
    private $fileSystemScanner;

    /** @var Analyser */
    private $analyser;

    /** @var EnvironmentDataLoaderInterface */
    private $environmentDataLoader;

    public function __construct(
        ComposerJson $composerJson,
        FileSystemScanner $fileSystemScanner,
        Analyser $analyser,
        EnvironmentDataLoader $environmentDataLoader
    ) {
        $this->composerJson = $composerJson;
        $this->fileSystemScanner = $fileSystemScanner;
        $this->analyser = $analyser;
        $this->environmentDataLoader = $environmentDataLoader;
    }

    public function load(): void
    {
        $this->composerJson->load();
        $this->fileSystemScanner->load();
        $this->environmentDataLoader->load();
    }

    public function getFilesLoaded(): int
    {
        return \count($this->fileSystemScanner->getFileLoaded());
    }

    public function totalSymbolsLoaded(): int
    {
        return
            ($this->environmentDataLoader->getCollectBaseClasses()->count()) +
            ($this->environmentDataLoader->getCollectBaseInterfaces()->count()) +
            ($this->environmentDataLoader->getCollectBaseFunctions()->count()) +
            ($this->environmentDataLoader->getCollectBaseConstants()->count());
    }

    public function process(): ResultParserInterface //todo: bad 
    { 
        /** @var ResultParserNamespaceValidate $result */
        $result = $this->processEntries($this->fileSystemScanner, $this->analyser);

        if ($result->withError()) {
            return new ResultParserNamespaceValidate($result->getCountErrors());
        }

        return $result;
    }

    private function processEntries(FileSystemScanner $fileSystemScanner, Analyser $analyser): ResultParserNamespaceValidate //todo: bad 
    {
        $totalResult = new ResultParserNamespaceValidate();
        foreach ($fileSystemScanner->getFileLoaded() as $file) {

            /** @var ResultParserNamespaceValidate $result */
            $result = $analyser->execute($file);

            $totalResult = $totalResult->append($result);
        }

        return $totalResult;
    }
}
