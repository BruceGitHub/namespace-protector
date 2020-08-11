<?php declare(strict_types=1);

namespace NamespaceProtector\OutputDevice;

use NamespaceProtector\Result\ResultProcessorInterface;

interface OutputDeviceInterface
{
    public function output(ResultProcessorInterface $value): void;
}
