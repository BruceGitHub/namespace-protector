<?php

namespace App;

use App\Common\PathInterface;
use App\Parser\ParserInteface;
use App\Parser\Node\PhpNode;

final class Analyser 
{
    private $listParser = []; 
    private $withError = false; 
    
    public function __construct(ParserInteface ...$listParser)
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

    public function getWithError(): bool
    {
        return $this->withError;
    }
}