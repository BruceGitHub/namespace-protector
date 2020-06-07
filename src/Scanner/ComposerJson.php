<?php
declare(strict_types=1);

namespace NamespaceProtector\Scanner;

use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;
use Webmozart\Assert\Assert;

final class ComposerJson implements ScannerInterface
{

    /** @var PathInterface  */
    private $fileSystemPathComposerJson;

    /** @var array<string> */
    private $psr4Ns;

    public function __construct(PathInterface $fileSystemPathComposerJson)
    {
        $this->fileSystemPathComposerJson = new FileSystemPath($fileSystemPathComposerJson->get().'/composer.json');
        Assert::readable($this->fileSystemPathComposerJson->get(), "Composer json file not readable");
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
