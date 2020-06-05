<?php

namespace NamespaceProtector\Scanner;

use NamespaceProtector\Common\PathInterface;

final class FileSystemScanner implements ScannerInterface
{

    /** @var array<PathInterface>  */
    private $startPaths;

    /** @var array<string>  */
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
                $fileLoaded[] = $pathDescriptor;
            }
        }

        $this->fileLoaded = $fileLoaded;
    }

    /**
     * @return array<string>
     */
    public function getFileLoaded(): array
    {
        return $this->fileLoaded;
    }
}
