<?php declare(strict_types=1);

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\PathInterface;
use Webmozart\Assert\Assert;

final class Config
{
    public const MODE_MAKE_VENDOR_PRIVATE = 'MODE_MAKE_VENDOR_PRIVATE';
    public const MODE_PUBLIC = 'PUBLIC';

    public const PLOTTER_TERMINAL = 'plotter-terminal';
    public const PLOTTER_PNG = 'plotter-png';

    /** @var PathInterface */
    private $pathStart;

    /** @var PathInterface  */
    private $pathComposerJson;

    /** @var array<string> */
    private $privateEntries;

    /** @var array<string> */
    private $publicEntries;

    /** @var string */
    private $mode;

    /** @var string */
    private $version;

    /** @var bool */
    private $enabledCache;

    /** @var string */
    private $plotter;

    /**
     * @param array<string> $privateEntries
     * @param array<string> $publicEntries
     */
    public function __construct(
        string $version,
        PathInterface $pathStart,
        PathInterface $pathComposerJson,
        array $privateEntries,
        array $publicEntries,
        string $mode = self::MODE_PUBLIC,
        bool $enabledCache = false,
        string $plotter = self::PLOTTER_TERMINAL
    ) {
        $this->version = $version;
        $this->pathStart = $pathStart;
        $this->pathComposerJson = $pathComposerJson;
        $this->privateEntries = $privateEntries;
        $this->publicEntries = $publicEntries;
        $this->mode = $mode;
        $this->enabledCache = $enabledCache;
        $this->plotter = $plotter;
    }

    public function getStartPath(): PathInterface
    {
        return $this->pathStart;
    }

    /**
     * @return array<string>
     */
    public function getPrivateEntries(): array
    {
        return $this->privateEntries;
    }

    /**
     * @return array<string>
     */
    public function getPublicEntries(): array
    {
        return $this->publicEntries;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getPlotter(): string
    {
        return $this->plotter;
    }

    public function getPathComposerJson(): PathInterface
    {
        return $this->pathComposerJson;
    }

    /** @param array<string,string> $parameters */
    public function cloneWithWithOverride(self $config, array $parameters): self
    {
        $self = new self(
            $config->getVersion(),
            $config->getStartPath(),
            $config->getPathComposerJson(),
            $config->getPrivateEntries(),
            $config->getPublicEntries(),
            $config->getMode(),
            $config->enabledCache(),
            $parameters['plotter'] ?? $config->getPlotter(),
        );

        $self->validateLoadedConfig(); //todo: validation called multiple times

        return $self;
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
        return $this->version;
    }

    public function enabledCache(): bool
    {
        return $this->enabledCache;
    }
}
