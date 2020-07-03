<?php
declare(strict_types=1);

namespace NamespaceProtector\Scanner;

use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;
use Webmozart\Assert\Assert;

final class ComposerJson implements ScannerInterface
{
    private const COMPOSER_JSON = 'composer.json';
    private const NAME_PROJECT = 'brucegithub/namespace-protector';

    /** @var PathInterface */
    private $fileSystemPathComposerJson;

    /** @var array<string> */
    private $psr4Ns;

    public function __construct(PathInterface $fileSystemPathComposerJson)
    {
        $this->fileSystemPathComposerJson = new FileSystemPath(
            $fileSystemPathComposerJson->get()
            . DIRECTORY_SEPARATOR
            . self::COMPOSER_JSON
        );

        Assert::readable($this->fileSystemPathComposerJson->get(), NamespaceProtectorExceptionInterface::MSG_PLAIN_ERROR_COMPOSER_JSON_NOT_READABLE);
    }

    //todo: dirty implemetation
    public static function detectComposerJsonDirectory(): PathInterface
    {
        $countMax = 5;
        $relativePath = '';

        for ($i = 0; $i < $countMax; $i++) {
            $pathComposer = \getcwd() . DIRECTORY_SEPARATOR . $relativePath;
            $realPath = \safe\realpath($pathComposer . self::COMPOSER_JSON);

            if (\is_readable($realPath) === false) {
                $relativePath .= '..' . DIRECTORY_SEPARATOR;
                continue;
            }

            $jsonArray = \safe\json_decode(
                \safe\file_get_contents($pathComposer . DIRECTORY_SEPARATOR . self::COMPOSER_JSON),
                true
            );

            if ($jsonArray['name'] !== self::NAME_PROJECT) {
                return new FileSystemPath($pathComposer);
            }
        }

        throw new \RuntimeException(NamespaceProtectorExceptionInterface::MSG_PLAIN_ERROR_COMPOSER_JSON_NOT_FOUND);
    }

    public function load(): void
    {
        $content = \safe\file_get_contents($this->fileSystemPathComposerJson->get());

        $data = \safe\json_decode($content, true);

        $this->psr4Ns = $data['autoload']['psr-4'];
    }

    /** @return  array<string> */
    public function getPsr4Ns(): array
    {
        return $this->psr4Ns;
    }
}
