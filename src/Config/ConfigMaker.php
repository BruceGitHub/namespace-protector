<?php declare(strict_types=1);

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Common\FileSystemPath;

final class ConfigMaker extends AbstractConfigMaker
{
    public function createFromFile(PathInterface $path): Config
    {
        $content = \safe\file_get_contents($path->get());
        $arrayConfig = \safe\json_decode($content, true);

        $self = new Config(
            $arrayConfig['version'],
            new FileSystemPath($arrayConfig['start-path'] ?? '.'),
            new FileSystemPath($arrayConfig['composer-json-path'] ?? '.'),
            $arrayConfig['private-entries'] ?? [],
            $arrayConfig['public-entries'] ?? [],
            $arrayConfig['mode'] ?? Config::MODE_PUBLIC,
            $arrayConfig['cache'] ?? false,
            $arrayConfig['plotter'] ?? Config::PLOTTER_TERMINAL,
        );

        $self->validateLoadedConfig();
        return $self;
    }

    /** @param array<string,string> $parameters */
    public function createFromItSelf(Config $config, array $parameters): Config
    {
        $self = new Config(
            $config->getVersion(),
            $config->getStartPath(),
            $config->getPathComposerJson(),
            $config->getPrivateEntries(),
            $config->getPublicEntries(),
            $config->getMode(),
            $config->enabledCache(),
            $parameters['plotter'] ?? $config->getPlotter(),
        );

        $self->validateLoadedConfig();

        return $self;
    }
}
