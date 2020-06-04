<?php

namespace NamespaceProtector\Command;

use Composer\Autoload\ClassLoader;
use NamespaceProtector\Analyser;
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
    private $composerJson;

    public function __construct(ComposerJson $composerJson, string $name = null)
    {
        parent::__construct($name);
        $this->composerJson = $composerJson;
    }

    protected function configure()
    {
        $this->setName('validate-namespace')
            ->setDescription('Validate namespace accessibility')
            ->setHelp('Validate for each namespace the access from another private namespace');
    }

    abstract public function getConfig(): Config;

    final protected function execute(InputInterface $input, OutputInterface $output): int
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
        $analyser = new Analyser(new PhpFileParser($config,$metaDataLoader));

        $output->writeln($config->print());
        $output->writeln("Load data....");

        $fileSystem->load();
        $output->writeln("Loaded " . count($fileSystem->getFileLoaded()) . ' files to validate');

        $totalSymbolsLoaded = $this->loadSymbols($metaDataLoader);
        $output->writeln('Loaded ' . $totalSymbolsLoaded . ' built in symbols');

        $output->writeln('Start analysis...');
        $this->processEntries($fileSystem, $analyser);

        $output->writeln('Total errors: '.$analyser->getCountErrors());

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
            count($metaDataLoader->getCollectBaseClasses()) +
            count($metaDataLoader->getCollectBaseInterfaces()) +
            count($metaDataLoader->getCollectBaseFunctions()) +
            count($metaDataLoader->getCollectBaseConstants());
    }

    private function processEntries(FileSystemScanner $fileSystem, Analyser $analyser): void
    {
        foreach ($fileSystem->getFileLoaded() as $file) {
            $analyser->execute(new FileSystemPath($file));
        }
    }
}
