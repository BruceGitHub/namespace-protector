<?php
declare(strict_types=1);

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\PathInterface;

final class ConfigTemplateCreator implements ConfigTemplateCreatorInterface
{
    private string $templateName;
    private string $configName;

    public function __construct(string $configName, string $templateName)
    {
        $this->templateName = $templateName;
        $this->configName = $configName;
    }

    public function create(PathInterface $destinationPathFileJson): void
    {
        $this->createFileWithBackup(
            $destinationPathFileJson->get() . $this->configName,
            $this->templateName
        );
    }

    private function createFileWithBackup(string $fileName, string $templateFile): void
    {
        @\rename($fileName, $fileName . '_backup.json');

        $content = \safe\file_get_contents(__DIR__ . '/' . $templateFile);
        \safe\file_put_contents($fileName, $content);
    }
}
