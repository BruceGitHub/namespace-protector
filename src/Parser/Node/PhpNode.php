<?php

namespace NamespaceProtector\Parser\Node;


use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;

use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\Result\Result;
use NamespaceProtector\Config;

final class PhpNode extends NameResolver
{
    public const ERR = 1;
    private $listNodeProcessor;
    private $resultCollector;
    private $metadataLoader;
    private $globalConfig;

    public function __construct(
        Config $configGlobal,
        array $configParser,
        ResultCollector $resultCollector,
        EnvironmentDataLoader $metadataLoader
    )
    {
        parent::__construct(null, $configParser);

        $this->globalConfig = $configGlobal;
        $this->metadataLoader = $metadataLoader;
        $this->resultCollector = $resultCollector;

        $this->listNodeProcessor[UseUse::class] = static function (Node $node): string {
            return $node->name->toCodeString();
        };

        $this->listNodeProcessor[FullyQualified::class] = static function (Node $node): string {
            return $node->toCodeString();
        };

    }

    public function enterNode(Node $node)
    {
        $this->processNode($node);
    }

    private function processNode(Node $node): void
    {
        $class = \get_class($node);
        if (!isset($this->listNodeProcessor[$class])) {
            return;
        }

        $func = $this->listNodeProcessor[$class];
        $val = $func($node);

        if (!$this->isFalsePositives($val)) {
            return;
        }

        if ($this->isVendorNamespace($val)) {
            return;
        }

        if ($this->isPublicEntry($val)) {
            return;
        }

        //todo: optimize (avoid if)
        if ($this->globalConfig->getMode() === Config::MODE_PRIVATE) {
            $this->pushError($val, $node);
            return;
        }

        $this->validateAccessToPrivateEntries($val, $node);

    }

    private function isFalsePositives(string $result): bool
    {
        $val = \str_replace('\\', '', $result);
        if (EnvironmentDataLoader::valueExist($this->metadataLoader->getCollectBaseClasses(), $val)) {
            return true;
        }

        if (EnvironmentDataLoader::valueExist($this->metadataLoader->getCollectBaseInterfaces(), $val)) {
            return true;
        }

        if (EnvironmentDataLoader::valueExist($this->metadataLoader->getCollectBaseFunctions(), $val)) {
            return true;
        }

        if (EnvironmentDataLoader::keyExist($this->metadataLoader->getCollectBaseConstants(), $val)) {
            return true;
        }

        return false;
    }

    private function isPublicEntry(string $entry): bool
    {
        if (\in_array($entry, $this->globalConfig->getPublicEntries(), true)) {
            return true;
        }

        return false;
    }

    private function validateAccessToPrivateEntries(string $val, Node $node): void
    {
        foreach ($this->globalConfig->getPrivateEntries() as $entry) {

            if (strpos($val, $entry) !== false) {
                $this->pushError($val, $node);
            }
        }
    }

    private function pushError(string $val, Node $node): void
    {
        $err = "\t > ERROR: of use $val. On Line: " .
            $node->getLine() .
            PHP_EOL;
        $this->resultCollector->addResult(new Result($err, self::ERR));
    }

    private function isVendorNamespace(string $val): bool
    {
        foreach ($this->metadataLoader->getCollectVendorNamespace() as $entry => $value) {

            if (strpos($val, $entry) === false) {
                return true;
            }
        }

        return false;
    }
}
