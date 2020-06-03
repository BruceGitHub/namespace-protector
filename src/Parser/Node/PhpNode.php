<?php

namespace NamespaceProtector\Parser\Node;


use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\Stmt\FullyQualified;

//lib namespace
use NamespaceProtector\MetadataLoader;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\Result\Result;
use NamespaceProtector\Config;

final class PhpNode extends NameResolver
{
    public const ERR=1; 
    private $listNodeProcessor = [];
    private $resultCollector;
    private $metadataLoader;
    private $globalConfig;

    public function __construct(
        Config $configGlobal,
        array $configParser,
        ResultCollector $resultCollector,
        MetadataLoader $metadataLoader
    ) {
        parent::__construct(null, $configParser);

        $this->globalConfig = $configGlobal;
        $this->metadataLoader = $metadataLoader;
        $this->resultCollector = $resultCollector;

        $this->listNodeProcessor[UseUse::class] = static function (Node $node): string {
            return $node->name->toCodeString();
        };

        $this->listNodeProcessor[FullyQualified::class] = static function (Node $node): string {
            return $node->name->toCodeString();
        };

    }

    public function enterNode(Node $node)
    {
        $this->processNode($node);
    }

    private function processNode(Node $node): void
    {
        $class = \get_class($node);
        if (isset($this->listNodeProcessor[$class])) {
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

            //todo: optimize
            if ($this->globalConfig->getMode()===Config::MODE_PRIVATE) {
                $this->pushError($val, $node);
                return;
            }

            $this->validateWithPrivateMode($val, $node);
        }
    }

    private function isFalsePositives(string $result): bool
    {
        $val = \str_replace('\\', '', $result);
        if (MetadataLoader::valueExist($this->metadataLoader->getCollectBaseClasses(),$val)) {
            return true;
        }

        if (MetadataLoader::valueExist($this->metadataLoader->getCollectBaseInterfaces(),$val)) {
            return true;
        }

        if (MetadataLoader::valueExist($this->metadataLoader->getCollectBaseFunctions(),$val)) {
            return true;
        }

        if (MetadataLoader::keyExist($this->metadataLoader->getCollectBaseConstants(),$val)) {
            return true;
        }

        return false;
    }

    private function isPublicEntry($entry): bool
    {
        if (\in_array($entry,$this->globalConfig->getPublicEntries() ,true)) {
            return true;
        }

        return false;
    }

    private function validateWithPrivateMode($val, Node $node): void
    {
        foreach ($this->globalConfig->getPrivateEntries() as $entry) {

            if (strpos($val, $entry) >= 1) {
                $this->pushError($val, $node);
            }
        }
    }

    private function pushError($val, Node $node): void
    {
        $err = "\t > ERROR: of use $val. it's PRIVATE namespace access, on Line: " .
            $node->getLine() .
            PHP_EOL;
        $this->resultCollector->addResult(new Result($err, self::ERR));
    }

    private function isVendorNamespace($val): bool
    {
        if (isset($this->metadataLoader->getCollectVendorNamespace()[$val])) {
            var_dump($this->metadataLoader->getCollectVendorNamespace()[$val]);
            return true;
        }

        return false;
    }
}
