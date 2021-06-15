<?php declare(strict_types=1);

namespace NamespaceProtector\Common;

use Webmozart\Assert\Assert;

final class FileSystemPath implements PathInterface
{
    public function __construct(private string $path, bool $noCheck = false)
    {
        if ($noCheck === false) {
            Assert::readable($path);
        }
    }

    public function __invoke(): string
    {
        return $this->get();
    }

    public function get(): string
    {
        return $this->path;
    }
}
