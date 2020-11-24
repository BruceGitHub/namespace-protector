<?php declare(strict_types=1);

namespace NamespaceProtector\Entry;

use Webmozart\Assert\Assert;

final class Entry
{
    private string $entry;

    public function __construct(string $entry)
    {
        Assert::notEmpty($entry);

        $this->entry = $entry;
    }

    public function equalTo(self $other): bool
    {
        return $this == $other;
    }

    public function get(): string
    {
        return $this->entry;
    }

    public function __invoke(): string
    {
        return $this->get();
    }
}
