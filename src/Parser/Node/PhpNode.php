<?php

namespace NamespaceProtector\Parser\Node;

use NamespaceProtector\Config;
use NamespaceProtector\Db\BooleanMatchKey;
use NamespaceProtector\Db\BooleanMatchPos;
use NamespaceProtector\Db\BooleanMatchValue;
use NamespaceProtector\Db\DbKeyValueInterface;
use NamespaceProtector\Db\MatchCollectionInterface;
use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\Result\Result;
use NamespaceProtector\Result\ResultCollector;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitor\NameResolver;

final class PhpNode extends NameResolver
{
    public const ERR = 1;

    /** @var array<Callable> */
    private $listNodeProcessor;

    /** @var ResultCollector  */
    private $resultCollector;

    /** @var EnvironmentDataLoader  */
    private $metadataLoader;

    /** @var Config  */
    private $globalConfig;

    /** @var MatchCollectionInterface */
    private $matchPos;

    /** @var BooleanMatchKey */
    private $matchKey;

    /** @var BooleanMatchValue */
    private $matchValue;

    /**
     * @param array<string,mixed> $configParser
     */
    public function __construct(
        Config $configGlobal,
        array $configParser,
        ResultCollector $resultCollector,
        EnvironmentDataLoader $metadataLoader
    ) {
        parent::__construct(null, $configParser);

        $this->globalConfig = $configGlobal;
        $this->metadataLoader = $metadataLoader;
        $this->resultCollector = $resultCollector;
        $this->matchPos = new BooleanMatchPos();
        $this->matchKey = new BooleanMatchKey();
        $this->matchValue = new BooleanMatchValue();

        $this->listNodeProcessor[UseUse::class] = static function (Node $node): string {

            /** @var UseUse $node*/
            return $node->name->toCodeString();
        };

        $this->listNodeProcessor[FullyQualified::class] = static function (Node $node): string {

            /** @var FullyQualified $node*/
            return $node->toCodeString();
        };
    }

    public function enterNode(Node $node)
    {
        $this->processNode($node);

        return $node;
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
        if ($result[0] ==='\\') {
            $result= substr($result, 1, strlen($result));
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseConstants(), $this->matchKey, $result)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseFunctions(), $this->matchValue, $result)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseInterfaces(), $this->matchValue, $result)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseClasses(), $this->matchValue, $result)) {
            return true;
        }

        return false;
    }

    private function isPublicEntry(string $currentNamespaceAccess): bool
    {
        foreach ($this->globalConfig->getPublicEntries() as $publicEntry) {
            if (strpos($currentNamespaceAccess, $publicEntry) !== false) {
                return true;
            }
        }

        return false;
    }

    private function validateAccessToPrivateEntries(string $currentNamespaceAccess, Node $node): void
    {
        foreach ($this->globalConfig->getPrivateEntries() as $privateEntry) {
            if (strpos($currentNamespaceAccess, $privateEntry) !== false) {
                $this->pushError($currentNamespaceAccess, $node);
            }
        }
    }

    private function pushError(string $val, Node $node): void
    {
        $err = "\t > ERROR Line: ".$node->getLine()." of use $val " . \PHP_EOL;
        $this->resultCollector->addResult(new Result($err, self::ERR));
    }

    private function isVendorNamespace(string $val): bool
    {
        if ($this->metadataLoader
            ->getCollectComposerNamespace()
            ->booleanSearch($this->matchPos, $val)) {
            return true;
        }

        return false;
    }

    private function valueExist(DbKeyValueInterface $collections, MatchCollectionInterface $matchCriteria, string $matchMe): bool
    {
        if ($collections->booleanSearch($matchCriteria, $matchMe)) {
            return true;
        }

        return false;
    }
}
