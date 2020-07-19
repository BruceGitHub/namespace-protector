<?php

namespace NamespaceProtector\Result;

class ResultParserNamespaceValidate implements ResultParserInterface
{

    /** @var int  */
    private $countErrors;

    public function __construct(int $count=0) 
    {
        $this->countErrors = $count;
    }
    public function withError(): bool 
    {
        return $this->getCountErrors()>=1 ? true : false; 
    }

    public function incrementError(): self 
    {
        return new self($this->getCountErrors()+1);
    }

    public function getCountErrors(): int  
    {
        return $this->countErrors;
    }

    public function append(self $toAppendInstance): self
    {
        return new self($toAppendInstance->getCountErrors()+$this->getCountErrors());
    }
    
}
