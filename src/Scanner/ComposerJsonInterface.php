<?php declare(strict_types=1);

namespace NamespaceProtector\Scanner;

interface ComposerJsonInterface extends ScannerInterface
{
    public function load(): void;

    /** @return  array<string> */
    public function getPsr4Ns(): array;
}
