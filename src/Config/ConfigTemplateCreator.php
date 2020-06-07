<?php
declare(strict_types=1);

namespace NamespaceProtector\Config;

use \safe;

final class ConfigTemplateCreator
{
    private const FILENAME = __DIR__ . '/../../namespace-protector-config.json';
    private const FILENAME_VISIBILITY = __DIR__ . '../../../namespace-protector-visibility.json';
    private const TEMPLATE_CONFIG_JSON = 'template-config-json';

    public function __construct()
    {
    }

    public static function createJsonTemplateConfig(): void
    {
        self::createFileWithBack(self::FILENAME,self::TEMPLATE_CONFIG_JSON);
    }

    public static function createJsonTemplateVisibility(): void
    {
        self::createFileWithBack(self::FILENAME_VISIBILITY,'template-visibility');
    }

    private static function createFileWithBack(string $fileName, string $templateFile): void
    {
        @rename($fileName, $fileName . '_back.json');

        $content = safe\file_get_contents(__DIR__ . '/'.  $templateFile);
        safe\file_put_contents($fileName, $content);
    }

}
