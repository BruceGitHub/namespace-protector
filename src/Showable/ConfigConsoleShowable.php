<?php

declare(strict_types=1);

namespace NamespaceProtector\Showable;

use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Config as RootConfig;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigConsoleShowable implements ConfigShowableInterface
{
    public function __construct(private OutputInterface $console)
    {
    }

    public function show(RootConfig\Config $config): void
    {
        $prettyPrintPrivateEntries = $this->populateOutputVarFromArray($config->getPrivateEntries());
        $prettyPrintPublicEntries = $this->populateOutputVarFromArray($config->getPublicEntries());

        $output =
            '' . PHP_EOL .
            '|Dump config:' . PHP_EOL .
            '|> Version: ' . $config->getVersion() . PHP_EOL .
            '|> Cache: ' . ($config->enabledCache() === true ? 'TRUE' : 'FALSE') . PHP_EOL .
            '|> Plotter: ' . $config->getPlotter() . PHP_EOL .
            '|> Path start: ' . $config->getStartPath()->get() . PHP_EOL .
            '|> Composer Json path: ' . $config->getPathComposerJson()->get() . PHP_EOL .
            '|> Mode: ' . $config->getMode() . PHP_EOL .
            '|> Private entries: ' . $prettyPrintPrivateEntries->toValue() . PHP_EOL .
            '|' . PHP_EOL .
            '|> Public entries: ' . $prettyPrintPublicEntries->toValue() . PHP_EOL .
            '';

        /** @var string $writeOutput */
        $writeOutput = $this->console->write($output);

        echo $writeOutput;
    }

    /**
     * @param array<mixed> $entries
     */
    private function populateOutputVarFromArray(array $entries): StringVo
    {
        $prettyPrintNamespaceToValidate = "\n";

        /** @psalm-suppress UnusedVariable */
        array_walk(
            $entries,
            fn (string $namespace): string => $prettyPrintNamespaceToValidate .= '|       >' . $namespace . \PHP_EOL,
        );

        return StringVo::fromValue($prettyPrintNamespaceToValidate);
    }
}
