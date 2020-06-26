<?php
namespace NamespaceProtector\Entry;

use Webmozart\Assert\Assert;

final class Entry
{

    /** @var string */
    private $entry;

    /** @var string */
    private $originalEntry;

    public function __construct(string $entry)
    {
        Assert::notEmpty($entry);

        $this->originalEntry = $entry;
        $this->entry =  $entry;
    }

    public function equalTo(self $other): bool
    {
        return $this == $other;
    }

    public function get(): string
    {
        return $this->entry;
    }
}
