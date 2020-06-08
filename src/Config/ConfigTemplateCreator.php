<?php
declare(strict_types=1);

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\PathInterface;

final class ConfigTemplateCreator
{
    private const FILENAME = 'namespace-protector-config.json';
    private const FILENAME_VISIBILITY =  'namespace-protector-visibility.json';
    private const TEMPLATE_CONFIG_JSON = 'template-config-json';

    public function __construct()
    {
    }

    public static function createJsonTemplateConfig(PathInterface $baseDir): void
    {
        self::createFileWithBack($baseDir->get().self::FILENAME, self::TEMPLATE_CONFIG_JSON);
    }

    public static function createJsonTemplateVisibility(): void
    {
        self::createFileWithBack(self::FILENAME_VISIBILITY, 'template-visibility');
    }

    private static function createFileWithBack(string $fileName, string $templateFile): void
    {
        @rename($fileName, $fileName . '_back.json');

        $content = \safe\file_get_contents(__DIR__ . '/' . $templateFile);
        \safe\file_put_contents($fileName, $content);
    }
}
