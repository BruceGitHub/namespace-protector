<?php
declare(strict_types=1);

namespace NamespaceProtector\Scanner;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;
use Webmozart\Assert\Assert;

final class ComposerJson implements ComposerJsonInterface
{
    public const FILE_NAME = 'composer.json';
    public const PROJECT_NAME_IN_COMPOSER = 'brucegithub/namespace-protector';

    private string $fileSystemPathComposerJson;

    /** @var array<string> */
    private array $psr4Ns;

    public function __construct(PathInterface $fileSystemPathComposerJson)
    {
        $this->fileSystemPathComposerJson = $fileSystemPathComposerJson->get()
            . DIRECTORY_SEPARATOR
            . self::FILE_NAME;

        Assert::readable($this->fileSystemPathComposerJson, NamespaceProtectorExceptionInterface::MSG_PLAIN_ERROR_COMPOSER_JSON_NOT_READABLE);

        $this->psr4Ns = [];
    }

    public function load(): void
    {
        $content = \safe\file_get_contents($this->fileSystemPathComposerJson);

        /**
         * @var array{autoload: array{psr-4:array{string}} } $data
         */
        $data = \safe\json_decode($content, true);

        $this->psr4Ns = $data['autoload']['psr-4'];
    }

    /** @return  array<string> */
    public function getPsr4Ns(): array
    {
        return $this->psr4Ns;
    }
}
