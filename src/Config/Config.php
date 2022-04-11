<?php declare(strict_types=1);

namespace NamespaceProtector\Config;

use MinimalVo\BaseValueObject\BooleanVo;
use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Common\PathInterface;
use Webmozart\Assert\Assert;

final class Config
{
    public const MODE_MAKE_VENDOR_PRIVATE = 'MODE_MAKE_VENDOR_PRIVATE';
    public const MODE_PUBLIC = 'PUBLIC';
    public const MODE_AUTODISCOVER = 'MODE_AUTODISCOVER';

    public const PLOTTER_TERMINAL = 'plotter-terminal';
    public const PLOTTER_PNG = 'plotter-png';

    public function __construct(
        private StringVo $version,
        private PathInterface $pathStart,
        private PathInterface $pathComposerJson,
        private array $privateEntries,
        private array $publicEntries,
        private StringVo $mode,
        private BooleanVo $enabledCache,
        private StringVo $plotter
    ) {
    }

    public function getStartPath(): PathInterface
    {
        return $this->pathStart;
    }

    public function getPrivateEntries(): array
    {
        return $this->privateEntries;
    }

    public function getPublicEntries(): array
    {
        return $this->publicEntries;
    }

    public function getMode(): string
    {
        return $this->mode->toValue();
    }

    public function getPlotter(): string
    {
        return $this->plotter->toValue();
    }

    public function getPathComposerJson(): PathInterface
    {
        return $this->pathComposerJson;
    }

    public function validateLoadedConfig(): void
    {
        Assert::inArray($this->getMode(), [self::MODE_PUBLIC, self::MODE_MAKE_VENDOR_PRIVATE], 'Mode not valid');
        Assert::eq('0.1.0', $this->getVersion(), 'Version not valid');
        Assert::directory($this->getStartPath()->get(), 'Start directory not valid');
        Assert::directory($this->getPathComposerJson()->get(), 'Composer json directory not valid');
        Assert::boolean($this->enabledCache(), 'Cache flag must be boolean');
        Assert::inArray($this->getPlotter(), [self::PLOTTER_TERMINAL, self::PLOTTER_PNG], 'Plotter not valid');
    }

    public function getVersion(): string
    {
        //todo: use https://github.com/nikolaposa/version
        return $this->version->toValue();
    }

    public function enabledCache(): bool
    {
        return $this->enabledCache->toValue();
    }
}
