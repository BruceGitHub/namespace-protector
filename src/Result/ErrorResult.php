<?php

namespace NamespaceProtector\Result;

final class ErrorResult implements ResultInterface
{
    /** @var int  */
    private $type;

    /** @var int */
    private $line;

    /** @var string */
    private $use;

    public function __construct(int $line, string $use, int $type)
    {
        $this->line = $line;
        $this->use = $use;
        $this->type = $type;
    }

    public function get(): String
    {
        return \Safe\sprintf("\t > ERROR Line: %d of use %s ", $this->line, $this->use);
    }

    public function getType(): int
    {
        return $this->type;
    }
}
