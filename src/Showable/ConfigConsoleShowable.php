<?php declare(strict_types=1);

namespace NamespaceProtector\Showable;

use NamespaceProtector\Config as RootConfig;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigConsoleShowable implements ConfigShowableInterface
{
    /** @var OutputInterface */
    private $console;

    public function __construct(OutputInterface $outputInterface)
    {
        $this->console = $outputInterface;
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
            '|> Private entries: ' . $prettyPrintPrivateEntries . PHP_EOL .
            '|' . PHP_EOL .
            '|> Public entries: ' . $prettyPrintPublicEntries . PHP_EOL .
            '';

        /** @var string $writeOutput */
        $writeOutput = $this->console->write($output);

        echo $writeOutput;
    }

    /**
     * @param array<mixed> $entries
     */
    private function populateOutputVarFromArray(array $entries): string
    {
        $prettyPrintNamespaceToValidate = "\n";

        /** @var string $namespace */
        foreach ($entries as $namespace) {
            $prettyPrintNamespaceToValidate .= '|       >' . $namespace . \PHP_EOL;
        }
        return $prettyPrintNamespaceToValidate;
    }
}
