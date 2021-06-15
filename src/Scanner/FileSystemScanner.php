<?php

declare(strict_types=1);

namespace NamespaceProtector\Scanner;

use DirectoryIterator;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Common\FileSystemPath;

final class FileSystemScanner implements ScannerInterface
{
    /** @var array<PathInterface>  */
    private array $fileLoaded = [];

    /**
     * @param array<PathInterface> $startPaths
     */
    public function __construct(private array $startPaths, private  string $extensions = 'php')
    {
    }

    public function load(): void
    {
        $fileLoaded = [];
        $this->fileLoaded = [];

        foreach ($this->startPaths as $pathDescriptor) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $pathDescriptor->get()
                )
            );

            /** @var DirectoryIterator $file */
            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    continue;
                }

                if ($file->getExtension() !== $this->extensions) {
                    continue;
                }

                $pathDescriptor = $file->getPathname();
                $fileLoaded[] = new FileSystemPath($pathDescriptor);
            }
        }

        $this->fileLoaded = $fileLoaded;
    }

    /**
     * @return array<PathInterface>
     */
    public function getFileLoaded(): array
    {
        return $this->fileLoaded;
    }
}
