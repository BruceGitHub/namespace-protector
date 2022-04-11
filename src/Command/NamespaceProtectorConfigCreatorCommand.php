<?php declare(strict_types=1);

namespace NamespaceProtector\Command;

use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Common\FileSystemPath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use NamespaceProtector\Config\ConfigTemplateCreatorInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;

final class NamespaceProtectorConfigCreatorCommand extends Command
{
    const CREATE_DEFAULT_CONFIG = 'create-default-config';
    const NAMESPACE_PROTECTOR_JSON = 'namespace-protector-config.json';
    const KEY_COMPOSER = 'brucegithub/namespace-protector';


    public function __construct(
        string $name,
        private ConfigTemplateCreatorInterface $configTemplateCreator
    ) {
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
        $this->configTemplateCreator->create($this->detectComposerJsonDirectory());
        return self::SUCCESS;
    }

    private function detectComposerJsonDirectory(): PathInterface
    {
        $countMax = 5;
        $relativePath = '';

        for ($i = 0; $i < $countMax; $i++) {
            $pathComposer = \getcwd() . DIRECTORY_SEPARATOR . $relativePath;
            $realPath = \safe\realpath($pathComposer . ComposerJson::FILE_NAME);

            if (\is_readable($realPath) === false) {
                $relativePath .= '..' . DIRECTORY_SEPARATOR;
                continue;
            }

            return new FileSystemPath(StringVo::fromValue($pathComposer));
        }

        throw new \RuntimeException(NamespaceProtectorExceptionInterface::MSG_PLAIN_ERROR_COMPOSER_JSON_NOT_READABLE);
    }
}
