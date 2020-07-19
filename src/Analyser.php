<?php declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Parser\Node\PhpNode;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultInterface;
use NamespaceProtector\Result\ResultParserInterface;
use NamespaceProtector\OutputDevice\OutputDeviceInterface;
use NamespaceProtector\Result\ResultParserNamespaceValidate;

final class Analyser
{
    /** @var ParserInterface[]  */
    private $listParser;

    /** @var OutputDeviceInterface */
    private $outputDevice;

    public function __construct(OutputDeviceInterface $outputDevice, ParserInterface ...$listParser)
    {
        $this->outputDevice = $outputDevice;
        $this->listParser = $listParser;
    }

    public function execute(PathInterface $pathInterface): ResultParserInterface
    {
        $resultParserNamespaceValidate = new ResultParserNamespaceValidate(); //todo: specific parser result 

        foreach ($this->listParser as $currentParser) {
            $currentParser->parseFile($pathInterface);

            //todo: specific parser result 
            foreach ($currentParser->getListResult()->get() as $result) {
                $this->outputDevice->output(($result->get())); 
                if ($result->getType() === PhpNode::ERR) { //todo: coupling with PhpNode 
                    $resultParserNamespaceValidate = $resultParserNamespaceValidate->incrementError(); //todo: specific operation processor
                }
            }
        }

        return $resultParserNamespaceValidate;
    }
}
