<?php

namespace NamespaceProtector\Common;

final class FileSystemPath implements PathInterface
{
    /** @var string  */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function get(): string
    {
        return $this->path;
    }
}
