<?php

namespace NamespaceProtector;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Parser\Node\PhpNode;

final class Analyser
{
    /** @var ParserInterface[]  */
    private $listParser;

    /** @var bool  */
    private $withError;

    /** @var int  */
    private $countErrors;

    public function __construct(ParserInterface ...$listParser)
    {
        $this->listParser = $listParser;
        $this->countErrors = 0;
        $this->withError = false;
    }

    public function execute(PathInterface $pathInterface): void
    {
        foreach ($this->listParser as $currentParser) {
            $currentParser->parseFile($pathInterface);
            
            foreach ($currentParser->getListResult()->get() as $result) {
                echo $result->get(); //todo: use output device
                if ($result->getType() === PhpNode::ERR) {
                    $this->withError = true;
                    $this->incrementError();
                }
            }
        }
    }

    public function withError(): bool
    {
        return $this->withError;
    }

    private function incrementError(): void
    {
        ++$this->countErrors;
    }

    public function getCountErrors(): int
    {
        return $this->countErrors;
    }
}
