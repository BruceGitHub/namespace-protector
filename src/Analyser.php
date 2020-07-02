<?php declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Parser\Node\PhpNode;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\OutputDevice\OutputDeviceInterface;

final class Analyser
{
    /** @var ParserInterface[]  */
    private $listParser;

    /** @var bool  */
    private $withError;

    /** @var int  */
    private $countErrors;

    /** @var OutputDeviceInterface */
    private $outputDevice;

    public function __construct(OutputDeviceInterface $outputDevice, ParserInterface ...$listParser)
    {
        $this->outputDevice = $outputDevice;
        $this->listParser = $listParser;
        $this->countErrors = 0;
        $this->withError = false;
    }

    public function execute(PathInterface $pathInterface): void
    {
        foreach ($this->listParser as $currentParser) {
            $currentParser->parseFile($pathInterface);

            foreach ($currentParser->getListResult()->get() as $result) {
                $this->outputDevice->output(($result->get()));
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
