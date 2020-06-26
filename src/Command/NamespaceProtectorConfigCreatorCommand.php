<?php

namespace NamespaceProtector\Command;

use NamespaceProtector\Scanner\ComposerJson;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use NamespaceProtector\Config\ConfigTemplateCreator;
use Symfony\Component\Console\Output\OutputInterface;

final class NamespaceProtectorConfigCreatorCommand extends Command
{
    const CREATE_DEFAULT_CONFIG = 'create-default-config';
    const NAMESPACE_PROTECTOR_JSON = 'namespace-protector.json';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setName('create-config')
            ->setDescription('Create config template')
            ->setHelp('Create config template with default values');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //todo: verify bug Symfony\Console with argument
        ConfigTemplateCreator::createJsonTemplateConfig(ComposerJson::detectComposerJsonDirectory());

        return self::SUCCESS;
    }
}
