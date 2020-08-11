<?php declare(strict_types=1);

namespace NamespaceProtector\Command;

use NamespaceProtector\Config\Config;
use NamespaceProtector\Common\FileSystemPath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use NamespaceProtector\Result\ResultProcessorInterface;
use NamespaceProtector\NamespaceProtectorProcessorFactory;
use NamespaceProtector\OutputDevice\ConsoleDevice;

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
        $output->writeln('Boot validate analysis....');
        $config = Config::loadFromFile(new FileSystemPath(\getcwd() . '/namespace-protector-config.json'));
        $factory = new NamespaceProtectorProcessorFactory();
        $namespaceProtectorProcessor = $factory->create($config);

        $namespaceProtectorProcessor->load();

        $output->writeln($config->print());
        $output->writeln('Load data...');
        $output->writeln('Loaded ' . $namespaceProtectorProcessor->getFilesLoaded() . ' files to validate');
        $output->writeln('Loaded ' . $namespaceProtectorProcessor->totalSymbolsLoaded() . ' built in symbols');
        $output->writeln('Start analysis...');

        /** @var ResultProcessorInterface $result */
        $result = $namespaceProtectorProcessor->process();

        $console = new ConsoleDevice($output);
        $console->output($result);

        return self::SUCCESS;
    }
}
