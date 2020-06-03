<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

//lib namespace 
use App\Scanner\FileSystemScanner;
use App\Config;
use App\MetadataLoader;
use App\Analyser;
use App\Parser\PhpFileParser;
use App\Common\FileSystemPath;

abstract class ValidateNamespaceCommand extends Command
{
    protected function configure()
    {
        $this->setName('validate-namespace')
            ->setDescription('Validate namespace accessibility')
            ->setHelp('Validate for each namespace the access from another private namespace');
    }

    abstract function getConfig(): Config;

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        //todo extract body method in specific namespace class 
        //todo load from json file 
        //todo use DI 
        $output->writeln("Boot validate analysis....");

        $config = $this->getConfig(); 

        $fileSystem = new FileSystemScanner([$config->getStartPath()]);
        $metaDataLoader = new MetadataLoader();
        $analyser = new Analyser(new PhpFileParser($config,$metaDataLoader));

        $output->writeln($config->print());
        $output->writeln("Load data....");

        $fileSystem->load();
        $output->writeln("Loaded " . count($fileSystem->getFileLoaded()) . ' files to validate');

        $totalSymbolsLoaded = $this->loadSymbols($metaDataLoader);
        $output->writeln('Loaded ' . $totalSymbolsLoaded . ' built in symbols');

        $output->writeln('Start analysis...');
        $this->processEntries($fileSystem, $analyser);

        if ($analyser->getWithError()) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function loadSymbols(MetadataLoader $metaDataLoader): int
    {
        $metaDataLoader->load();

        $totalSymbolsLoaded =
            count($metaDataLoader->getCollectBaseClasses()) +
            count($metaDataLoader->getCollectBaseInterfaces()) +
            count($metaDataLoader->getCollectBaseFunctions()) +
            count($metaDataLoader->getCollectBaseConstants());

        return $totalSymbolsLoaded;
    }

    private function processEntries(FileSystemScanner $fileSystem, Analyser $analyser): void
    {
        foreach ($fileSystem->getFileLoaded() as $file) {
            $analyser->execute(new FileSystemPath($file));
        }
    }
}
