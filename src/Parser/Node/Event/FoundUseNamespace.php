<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node\Event;

class FoundUseNamespace implements EventProcessNodeInterface
{
    /** @var int */
    private $line;

    /** @var string */
    private $nodeName;

    /** @var bool */
    private $erroDetect = false;

    public function __construct(int $line, string $nodeName)
    {
        $this->line = $line;
        $this->nodeName = $nodeName;
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
