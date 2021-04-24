<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node\Event;

final class FoundUseNamespace implements EventProcessNodeInterface
{
    private string $additionalInformation;

    private bool $erroDetect = false;

    public function __construct(private int $line, private string $nodeName)
    {
        $this->additionalInformation = '';
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getNodeName(): string
    {
        return $this->nodeName;
    }

    public function foundError(string $addtionalInformation = ''): void
    {
        $this->additionalInformation = $addtionalInformation;
        $this->erroDetect = true;
    }

    public function withError(): bool
    {
        return $this->erroDetect;
    }

    public function getAdditionalInformation(): string
    {
        return $this->additionalInformation;
    }
}
