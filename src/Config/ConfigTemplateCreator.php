<?php
declare(strict_types=1);

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\PathInterface;

final class ConfigTemplateCreator implements ConfigTemplateCreatorInterface
{
    /** @var string */
    private $templateName;

    public function __construct(string $templateName)
    {
        $this->templateName = $templateName;
    }

    public function create(PathInterface $destinationPathFileJson): void
    {
        $this->createFileWithBackup($destinationPathFileJson->get(), $this->templateName);
    }

    private function createFileWithBackup(string $fileName, string $templateFile): void
    {
        @\rename($fileName, $fileName . '_backup.json');

        $content = \safe\file_get_contents(__DIR__ . '/' . $templateFile);
        \safe\file_put_contents($fileName, $content);
    }
}
