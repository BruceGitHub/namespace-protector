<?php

namespace NamespaceProtector;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Parser\Node\PhpNode;

final class Analyser 
{
    private $listParser;
    private $withError = false; 
    
    public function __construct(ParserInterface ...$listParser)
    {
        $this->listParser = $listParser; 
    }

    public function execute(PathInterface $pathInterface): void
    {
        foreach ($this->listParser as $currentParser) 
        {
            $currentParser->parseFile($pathInterface);
            
            foreach ($currentParser->getListResult()->get() as $result) 
            {
              echo $result->get();   
              if ($result->getType()===PhpNode::ERR) {
                  $this->withError = true; 
              }  
            }
        }
    }

    public function withError(): bool
    {
        return $this->withError;
    }
}
