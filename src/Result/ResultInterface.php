<?php

namespace NamespaceProtector\Result;

interface ResultInterface
{
    public function get(): string;

    public function getType(): int;
}
