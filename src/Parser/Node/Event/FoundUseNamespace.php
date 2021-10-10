<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node\Event;

final class FoundUseNamespace implements EventProcessNodeInterface
{
    private bool $erroDetect = false;

    public function __construct(private int $line, private string $nodeName)
    {
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getNodeName(): string
    {
        return $this->nodeName;
    }

    public function foundError(): void
    {
        $this->erroDetect = true;
    }

    public function withError(): bool
    {
        return $this->erroDetect;
    }
}
