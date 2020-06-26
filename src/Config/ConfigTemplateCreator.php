<?php
declare(strict_types=1);

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\PathInterface;

final class ConfigTemplateCreator
{
    private const FILENAME = 'namespace-protector-config.json';
    private const FILENAME_VISIBILITY =  'namespace-protector-visibility.json';
    private const TEMPLATE_CONFIG_JSON = 'template-config-json';

    public static function createJsonTemplateConfig(PathInterface $baseComposerJsonDirectory): void
    {
        self::createFileWithBackup($baseComposerJsonDirectory().self::FILENAME, self::TEMPLATE_CONFIG_JSON);
    }

    public static function createJsonTemplateVisibility(): void
    {
        self::createFileWithBackup(self::FILENAME_VISIBILITY, 'template-visibility');
    }

    private static function createFileWithBackup(string $fileName, string $templateFile): void
    {
        @rename($fileName, $fileName . '_backup.json');

        $content = \safe\file_get_contents(__DIR__ . '/' . $templateFile);
        \safe\file_put_contents($fileName, $content);
    }
}
