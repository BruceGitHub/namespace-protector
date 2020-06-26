<?php

namespace NamespaceProtector\Command;

use NamespaceProtector\Analyser;
use NamespaceProtector\Cache\NullCache;
use NamespaceProtector\Cache\SimpleFileCache;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Config\Config;
use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\Parser\PhpFileParser;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Scanner\FileSystemScanner;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractValidateNamespaceCommand extends Command
{
    private const NAMESPACE_PROTECTOR_CACHE = 'namespace-protector-cache';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setName('validate-namespace')
            ->setDescription('Validate namespace')
            ->setHelp('Validate if some namespace access to one private namespace');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //todo extract body method in specific namespace class
        //todo use DI
        
        $output->writeln("Boot validate analysis....");

        $config = Config::loadFromFile(new FileSystemPath(\getcwd().'/namespace-protector-config.json'));

        $composerJson = new ComposerJson($config->getPathComposerJson());
        $composerJson->load();

        $fileSystem = new FileSystemScanner([$config->getStartPath()]);
        $metaDataLoader = new EnvironmentDataLoader($composerJson);

        $directory = \sys_get_temp_dir().self::NAMESPACE_PROTECTOR_CACHE;
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
            $analyser->execute($file);
        }
    }

    protected function createCacheObject(string $directory): CacheInterface
    {
        return new NullCache();
        return new SimpleFileCache(new FileSystemPath($directory,true));
    }
}
