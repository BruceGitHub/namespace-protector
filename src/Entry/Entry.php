<?php declare(strict_types=1);

namespace NamespaceProtector\Entry;

use MinimalVo\BaseValueObject\StringVo;
use Webmozart\Assert\Assert;

final class Entry
{
    public function __construct(private StringVo $entry)
    {
        Assert::notEmpty($entry);
    }

    public function equalTo(self $other): bool
    {
        return $this == $other;
    }

    public function get(): string
    {
        return $this->entry->toValue();
    }

    public function __invoke(): string
    {
        return $this->get();
    }
}
