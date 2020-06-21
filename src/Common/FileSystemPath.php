<?php

namespace NamespaceProtector\Common;

use Webmozart\Assert\Assert;

final class FileSystemPath implements PathInterface
{
    /** @var string  */
    private $path;

    public function __construct(string $path, bool $noCheck=false)
    {
        if ($noCheck === false) {
            Assert::readable($path);
        } 

        $this->path = $path;
    }

    public function get(): string
    {
        return $this->path;
    }
}
