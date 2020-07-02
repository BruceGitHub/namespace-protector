<?php declare(strict_types=1);

namespace NamespaceProtector\Common;

interface PathInterface
{
    public function __invoke(): string;

    public function get(): string;
}
