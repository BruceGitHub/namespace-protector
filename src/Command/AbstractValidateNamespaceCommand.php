<?php

namespace NamespaceProtector\Command;

use NamespaceProtector\Config\Config;
use NamespaceProtector\Common\FileSystemPath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use NamespaceProtector\NamespaceProtectorProcessorFactory;

abstract class AbstractValidateNamespaceCommand extends Command
{
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
        $output->writeln("Boot validate analysis....");
        $config = Config::loadFromFile(new FileSystemPath(\getcwd().'/namespace-protector-config.json'));
        $factory = new NamespaceProtectorProcessorFactory();
        $namespaceProtectorProcessor = $factory->create($config);

        $namespaceProtectorProcessor->load();

        $output->writeln($config->print());
        
        $output->writeln("Load data....");
        $output->writeln('Loaded ' . $namespaceProtectorProcessor->getFilesLoaded() . ' files to validate');
        $output->writeln('Loaded ' . $namespaceProtectorProcessor->totalSymbolsLoaded() . ' built in symbols');
        
        $output->writeln('Start analysis...');
        $namespaceProtectorProcessor->process();

        $output->writeln('<fg=red>Total errors: ' . $namespaceProtectorProcessor->getCountErrors().'</>');

        if ($namespaceProtectorProcessor->getCountErrors()>0) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
