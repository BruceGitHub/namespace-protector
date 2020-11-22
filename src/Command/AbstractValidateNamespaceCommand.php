<?php declare(strict_types=1);

namespace NamespaceProtector\Command;

use NamespaceProtector\Config\Config;
use NamespaceProtector\Config\ConfigMaker;
use NamespaceProtector\Common\FileSystemPath;
use Symfony\Component\Console\Command\Command;
use NamespaceProtector\OutputDevice\ConsoleDevice;
use Symfony\Component\Console\Input\InputArgument;
use NamespaceProtector\OutputDevice\GraphicsDevice;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use NamespaceProtector\Showable\ConfigConsoleShowable;
use NamespaceProtector\Result\ResultProcessorInterface;
use NamespaceProtector\NamespaceProtectorProcessorFactory;
use NamespaceProtector\OutputDevice\OutputDeviceInterface;

abstract class AbstractValidateNamespaceCommand extends Command
{
    private const PLOTTER_ARGUMENT = 'plotter';

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

        $this
            ->addArgument(self::PLOTTER_ARGUMENT, InputArgument::OPTIONAL, 'How show the output?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Boot validate analysis....');

        $configMaker = new ConfigMaker();
        $config = $configMaker->createFromFile(new FileSystemPath(\getcwd() . '/namespace-protector-config.json'));
        $plotter = $input->getArgument(self::PLOTTER_ARGUMENT);
        if ($plotter && \is_string($plotter)) {
            $config = $configMaker->createFromItSelf($config, ['plotter' => $plotter]);
        }

        $factory = new NamespaceProtectorProcessorFactory();
        $namespaceProtectorProcessor = $factory->create($config);
        $namespaceProtectorProcessor->load();

        $configConsole = new ConfigConsoleShowable($output);
        $configConsole->show($config);

        $output->writeln('Load data...');
        $output->writeln('Loaded ' . $namespaceProtectorProcessor->getFilesLoaded() . ' files to validate');
        $output->writeln('Loaded ' . $namespaceProtectorProcessor->totalSymbolsLoaded() . ' built in symbols');
        $output->writeln('Start analysis...');

        $result = $namespaceProtectorProcessor->process();

        $plotter = $this->createOutputObject($config, $output);
        $plotter->output($result);

        return self::SUCCESS;
    }

    private function createOutputObject(Config $config, OutputInterface $outputInterface): OutputDeviceInterface
    {
        if ($config->getPlotter() === Config::PLOTTER_TERMINAL) {
            return new ConsoleDevice($outputInterface);
        }

        return new GraphicsDevice();
    }
}
