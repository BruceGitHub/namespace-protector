<?php

namespace App\Scanner;

final class FileSystemScanner implements ScannerInterface {

    private $startPath;
    private $fileLoaded = [];
    private $extensions = 'php';

    public function __construct(
        array $startPaths, 
        string $extensions = 'php')
    {
        $this->startPaths = $startPaths; 
    }

    public function load(): void
    {
        $fileLoaded = array();
        $this->fileLoaded = array();

        foreach ($this->startPaths as $pathDescriptor) {
    
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($pathDescriptor->get()
                )
            );
            foreach ($iterator as $file) {
                if ($file->isDir()) continue;
                if ($file->getExtension() !== $this->extensions) continue; 
    
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