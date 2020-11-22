<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node\Event;

final class FoundUseNamespace implements EventProcessNodeInterface
{
    /** @var int */
    private $line;

    /** @var string */
    private $nodeName;

    /** @var string */
    private $additionalInformation;

    /** @var bool */
    private $erroDetect = false;

    public function __construct(int $line, string $nodeName)
    {
        $this->line = $line;
        $this->nodeName = $nodeName;
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
