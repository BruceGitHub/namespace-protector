<?php

namespace NamespaceProtector\OutputDevice;

final class ConsoleDevice implements OutputDeviceInterface
{
    public function output(string $value): void
    {
        echo $value;
    }
}
