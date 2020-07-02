<?php

namespace NamespaceProtector\Common;

interface PathInterface
{
    public function __invoke(): string;

    public function get(): string;
}
