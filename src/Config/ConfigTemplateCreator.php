<?php
declare(strict_types=1);

namespace NamespaceProtector\Config;

use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Common\PathInterface;

final class ConfigTemplateCreator implements ConfigTemplateCreatorInterface
{
    public function __construct(private StringVo $configName, private StringVo $templateName)
    {
    }

    public function create(PathInterface $destinationPathFileJson): void
    {
        $this->createFileWithBackup(
            StringVo::fromValue($destinationPathFileJson->get() . $this->configName->toValue()),
            $this->templateName
        );
    }

    private function createFileWithBackup(StringVo $fileName, StringVo $templateFile): void
    {
        @\rename($fileName->toValue(), $fileName->toValue() . '_backup.json');

        $content = \safe\file_get_contents(__DIR__ . '/' . $templateFile->toValue());
        \safe\file_put_contents($fileName->toValue(), $content);
    }
}
