<?php declare(strict_types=1);

namespace NamespaceProtector\Config;

use MinimalVo\BaseValueObject\BooleanVo;
use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Common\FileSystemPath;

final class ConfigMaker extends AbstractConfigMaker
{
    public function createFromFile(PathInterface $path): Config
    {
        $content = \safe\file_get_contents($path->get());

        /**
         * @var array
         * {
         *  version: string,
         *  start-path?: string,
         *  private-entries?: [],
         *  public-entries?: [],
         *  mode?: string,
         *  cache?: bool,
         *  plotter?: string,
         * } $parameters
         * */
        $parameters = \safe\json_decode($content, true);

        /** @var string $version */
        $version = $parameters['version'];

        /** @var string $startPath */
        $startPath = $parameters['start-path'] ?? '.';

        /** @var string $composerJsonPath */
        $composerJsonPath = $parameters['composer-json-path'] ?? '.';

        /** @var array<string> $privateEntries */
        $privateEntries = $parameters['private-entries'] ?? [];

        /** @var array<string> $publicEntries */
        $publicEntries = $parameters['public-entries'] ?? [];

        /** @var string $mode */
        $mode = $parameters['mode'] ?? Config::MODE_PUBLIC;

        /** @var bool $cache */
        $cache = $parameters['cache'] ?? false;

        /** @var string $plotter */
        $plotter = $parameters['plotter'] ?? Config::PLOTTER_TERMINAL;

        $self = new Config(
            StringVo::fromValue($version),
            new FileSystemPath(StringVo::fromValue($startPath)),
            new FileSystemPath(StringVo::fromValue($composerJsonPath)),
            $privateEntries,
            $publicEntries,
            StringVo::fromValue($mode),
            BooleanVo::fromValue($cache),
            StringVo::fromValue($plotter),
        );

        $self->validateLoadedConfig();
        return $self;
    }

    public function createFromItSelf(Config $config, array $parameters): Config
    {
        $self = new Config(
            StringVo::fromValue($config->getVersion()),
            $config->getStartPath(),
            $config->getPathComposerJson(),
            $config->getPrivateEntries(),
            $config->getPublicEntries(),
            StringVo::fromValue($config->getMode()),
            BooleanVo::fromValue($config->enabledCache()),
            StringVo::fromValue($parameters['plotter'] ?? $config->getPlotter()),
        );

        $self->validateLoadedConfig();

        return $self;
    }
}
