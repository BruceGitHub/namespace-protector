<?php
declare(strict_types=1);

namespace NamespaceProtector\Scanner;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;

final class ComposerJson implements ScannerInterface
{

    /** @var PathInterface  */
    private $fileSystemPathComposerJson;

    /** @var array<string> */
    private $psr4Ns;

    public function __construct(PathInterface $fileSystemPathComposerJson)
    {
        $this->fileSystemPathComposerJson = $fileSystemPathComposerJson;
    }

    public function load(): void
    {
        $content = file_get_contents($this->fileSystemPathComposerJson->get());
        if ($content === false) {
            throw new \RuntimeException(NamespaceProtectorExceptionInterface::MSG_PLAINE_JSON_EXCEPTION.': ' . json_last_error_msg());
        }

        $data = \safe\json_decode($content, true);

        $this->psr4Ns = $data['autoload']['psr-4'];
    }

    /** @return  array<string> */
    public function getPsr4Ns(): array
    {
        return $this->psr4Ns;
    }
}
