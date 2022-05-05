<?php declare(strict_types=1);

namespace NamespaceProtector\Common;

use MinimalVo\BaseValueObject\BooleanVo;
use MinimalVo\BaseValueObject\StringVo;
use Webmozart\Assert\Assert;

final class FileSystemPath implements PathInterface
{
    public function __construct(private StringVo $path, BooleanVo $noCheck = new BooleanVo(false))
    {
        if ($noCheck === BooleanVo::fromValue(false)) {
            Assert::readable($path->toValue());
        }
    }

    public function __invoke(): string
    {
        return $this->get();
    }

    public function get(): string
    {
        return $this->path->toValue();
    }
}
