<?php

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Common\PathInterface;
use Webmozart\Assert\Assert;

final class Config
{
    public const MODE_MAKE_VENDOR_PRIVATE = 'MODE_MAKE_VENDOR_PRIVATE';
    public const MODE_PUBLIC = 'PUBLIC';

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
        string $mode = self::MODE_PUBLIC
    ) {
        $this->version = $version;
        $this->pathStart = $pathStart;
        $this->pathComposerJson = $pathComposerJson;
        $this->privateEntries = $privateEntries;
        $this->publicEntries = $publicEntries;
        $this->mode = $mode;
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

    public function getPathComposerJson(): PathInterface
    {
        return $this->pathComposerJson;
    }


    public function print(): string
    {
        //todo: automatic dump config

        $prettyPrintPrivateEntries = $this->populateOutputVarFromArray($this->getPrivateEntries());
        $prettyPrintPublicEntries = $this->populateOutputVarFromArray($this->getPublicEntries());

        return
            '' . PHP_EOL .
            '|Dump config:' . PHP_EOL .
            '|> Version: ' . $this->getVersion() . PHP_EOL .
            '|> Path start: ' . $this->pathStart->get() . PHP_EOL .
            '|> Composer Json path: ' . $this->pathComposerJson->get() . PHP_EOL .
            '|> Mode: ' . $this->getMode() . PHP_EOL .
            '|> Private entries: ' . $prettyPrintPrivateEntries . PHP_EOL .
            '|' . PHP_EOL .
            '|> Public entries: ' . $prettyPrintPublicEntries . PHP_EOL .
            '';
    }

    /**
     * @param array<string> $entries
     */
    private function populateOutputVarFromArray(array $entries): string
    {
        $prettyPrintNamespaceToValidate = "\n";
        foreach ($entries as $namespace) {
            $prettyPrintNamespaceToValidate .= "|       >" . $namespace;
        }
        return $prettyPrintNamespaceToValidate;
    }

    public static function loadFromFile(PathInterface $path): self
    {
        $content = \safe\file_get_contents($path->get());
        $arrayConfig = \safe\json_decode($content, true);

        $self = new self(
            $arrayConfig['version'],
            new FileSystemPath($arrayConfig['start-path']),
            new FileSystemPath($arrayConfig['composer-json-path']),
            $arrayConfig['private-entries'],
            $arrayConfig['public-entries'],
            $arrayConfig['mode']
        );

        $self->validateLoadedConfig();

        return $self;
    }

    private function validateLoadedConfig(): void
    {
        //todo: adds more complex validation structure

        Assert::inArray($this->getMode(), [self::MODE_PUBLIC, self::MODE_MAKE_VENDOR_PRIVATE], "Mode not valid");
        Assert::eq('0.1.0', $this->getVersion(), "Version not valid");
        Assert::directory($this->getStartPath()->get(), "Start directory not valid");
        Assert::directory($this->getPathComposerJson()->get(), "Composer json directory not valid");
    }

    private function getVersion(): string
    {
        //todo: use https://github.com/nikolaposa/version
        return $this->version;
    }
}
