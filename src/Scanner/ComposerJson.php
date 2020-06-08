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

    /** @var PathInterface */
    private $fileSystemPathComposerJson;

    /** @var array<string> */
    private $psr4Ns;

    public function __construct(PathInterface $fileSystemPathComposerJson)
    {
        $this->fileSystemPathComposerJson = new FileSystemPath($fileSystemPathComposerJson->get() . '/' . self::COMPOSER_JSON);
        Assert::readable($this->fileSystemPathComposerJson->get(), "Composer json file not readable");
    }

    //todo: dirty implemetation
    public static function detectComposerJsonDirectory(): PathInterface
    {
        $countMax = 10;
        $relativePath = '';

        for ($i = 0; $i < $countMax; $i++) {
            $relativePath .= '..'.DIRECTORY_SEPARATOR;
            $pathComposer = __DIR__ .DIRECTORY_SEPARATOR. $relativePath;
            if (file_exists($pathComposer . self::COMPOSER_JSON) === true) {
                return new FileSystemPath($pathComposer);
            }
        }

        throw new \RuntimeException(NamespaceProtectorExceptionInterface::MSG_PLAIN_ERROR_COMPOSE_JSON_NOT_FOUND);
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
