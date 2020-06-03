<?php

namespace NamespaceProtector\Scanner;

final class FileSystemScanner implements ScannerInterface {

    private $startPath;
    private $fileLoaded = [];
    private $extensions;

    public function __construct(array $startPaths, string $extensions = 'php')
    {
        $this->startPath = $startPaths;
        $this->extensions = $extensions;
    }

    public function load(): void
    {
        $fileLoaded = [];
        $this->fileLoaded = [];

        foreach ($this->startPath as $pathDescriptor) {
    
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($pathDescriptor->get()
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

    public function getFileLoaded(): array 
    {
        return $this->fileLoaded;
    }

}
