<?php
declare(strict_types=1);

namespace NamespaceProtector\Scanner;

use NamespaceProtector\Common\PathInterface;

final class ComposerJson implements ScannerInterface
{
    private $fileSystemPathComposerJson;
    private $psr4Ns;

    public function __construct(PathInterface $fileSystemPathComposerJson)
    {
        $this->fileSystemPathComposerJson = $fileSystemPathComposerJson;
    }

    public function load(): void
    {

        $content = file_get_contents($this->fileSystemPathComposerJson->get());
        $data = \json_decode($content, true);
        if ($data === null && JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('Error loading composer.json' . ': ' . json_last_error_msg());
        }

        $this->psr4Ns = $data['autoload']['psr-4'];
    }

    public function getPsr4Ns(): array
    {
        return $this->psr4Ns;
    }
}
