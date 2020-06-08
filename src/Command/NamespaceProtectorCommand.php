<?php

namespace NamespaceProtector\Command;

use NamespaceProtector\Config\ConfigTemplateCreator;
use NamespaceProtector\Scanner\ComposerJson;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class NamespaceProtectorCommand extends AbstractValidateNamespaceCommand
{
    const CREATE_DEFAULT_CONFIG = 'create-default-config';
    const NAMESPACE_PROTECTOR_JSON = 'namespace-protector.json';

    public function configure(): void
    {
        $this-> addArgument(
            self::CREATE_DEFAULT_CONFIG,
            InputArgument::OPTIONAL,
            'Create default config file '. self::NAMESPACE_PROTECTOR_JSON
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //todo: verify bug Symfony\Console with argument
        //todo: split in two command
        if ($input->getArgument(self::CREATE_DEFAULT_CONFIG)) {
            ConfigTemplateCreator::createJsonTemplateConfig(ComposerJson::detectComposerJsonDirectory());

            return self::SUCCESS;
        }

        $script_start =  $this->startWatch();

        $returnValue = parent::execute($input, $output);

        $elapsed_time = $this->stopWatch($script_start);

        $output->writeln("<fg=green>Elapsed time: ".$elapsed_time.'</>');

        return $returnValue ;
    }

    private function startWatch(): float
    {
        list($usec, $sec) = explode(' ', microtime());
        return  (float)$sec + (float)$usec;
    }

    private function stopWatch(float $script_start): float
    {
        list($usec, $sec) = explode(' ', microtime());
        $script_end = (float)$sec + (float)$usec;

        return  round($script_end - $script_start, 5);
    }
}
