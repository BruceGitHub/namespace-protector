<?php declare(strict_types=1);

namespace NamespaceProtector\OutputDevice;

interface OutputDeviceInterface
{
    public function output(string $value): void;
}
