<?php

namespace NamespaceProtector\Scanner;

use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Common\PathInterface;

final class FileSystemScanner implements ScannerInterface
{
    /** @var array<PathInterface>  */
    private $startPaths;

    /** @var array<PathInterface>  */
    private $fileLoaded = [];

    /** @var string  */
    private $extensions;

    /**
     * @param array<PathInterface> $startPaths
     */
    public function __construct(array $startPaths, string $extensions = 'php')
    {
        $this->startPaths = $startPaths;
        $this->extensions = $extensions;
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
