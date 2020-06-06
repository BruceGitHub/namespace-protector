<?php

namespace NamespaceProtector\Command;

use NamespaceProtector\Analyser;
use NamespaceProtector\Cache\SimpleFileCache;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Config;
use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Scanner\FileSystemScanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ValidateNamespaceCommand extends Command
{
    private const NAMESPACE_PROTECTOR_CACHE = 'namespace-protector-cache';

    /** @var ComposerJson  */
    private $composerJson;

    public function __construct(ComposerJson $composerJson, string $name = null)
    {
        parent::__construct($name);
        $this->composerJson = $composerJson;
    }

    protected function configure(): void
    {
        $this->setName('validate-namespace')
            ->setDescription('Validate namespace accessibility')
            ->setHelp('Validate for each namespace the access from another private namespace');
    }

    abstract public function getConfig(): Config;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //todo extract body method in specific namespace class
        //todo load from json file
        //todo use DI
        $output->writeln("Boot validate analysis....");

        $config = $this->getConfig();
        $composerJson = $this->getComposerJson();
        $composerJson->load();

        $fileSystem = new FileSystemScanner([$config->getStartPath()]);
        $metaDataLoader = new EnvironmentDataLoader($composerJson);

        $directory = sys_get_temp_dir().\DIRECTORY_SEPARATOR.self::NAMESPACE_PROTECTOR_CACHE;
        $cacheClass = $this->createCacheObject($directory);

        $analyser = new Analyser(new PhpFileParser($config, $metaDataLoader, $cacheClass));

        $output->writeln($config->print());
        $output->writeln("Load data....");

        $fileSystem->load();
        $output->writeln("Loaded " . count($fileSystem->getFileLoaded()) . ' files to validate');

        $totalSymbolsLoaded = $this->loadSymbols($metaDataLoader);
        $output->writeln('Loaded ' . $totalSymbolsLoaded . ' built in symbols');

        $output->writeln('Start analysis...');
        $this->processEntries($fileSystem, $analyser);

        $output->writeln('<fg=red>Total errors: ' . $analyser->getCountErrors().'</>');

        if ($analyser->withError()) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    public function getComposerJson(): ComposerJson
    {
        return $this->composerJson;
    }

    private function loadSymbols(EnvironmentDataLoader $metaDataLoader): int
    {
        $metaDataLoader->load();

        return
            ($metaDataLoader->getCollectBaseClasses()->count()) +
            ($metaDataLoader->getCollectBaseInterfaces()->count()) +
            ($metaDataLoader->getCollectBaseFunctions()->count()) +
            ($metaDataLoader->getCollectBaseConstants()->count());
    }

    private function processEntries(FileSystemScanner $fileSystem, Analyser $analyser): void
    {
        foreach ($fileSystem->getFileLoaded() as $file) {
            $analyser->execute(new FileSystemPath($file));
        }
    }

    protected function createCacheObject(string $directory): SimpleFileCache
    {
        return new SimpleFileCache(new FileSystemPath($directory));
    }
}
