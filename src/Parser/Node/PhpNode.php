<?php

namespace NamespaceProtector\Parser\Node;

use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Result\Result;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitor\NameResolver;
use NamespaceProtector\Db\BooleanMatchKey;
use NamespaceProtector\Db\BooleanMatchPos;
use NamespaceProtector\Db\BooleanMatchValue;
use NamespaceProtector\Db\DbKeyValueInterface;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\Db\MatchCollectionInterface;
use NamespaceProtector\EnvironmentDataLoaderInterface;

final class PhpNode extends NameResolver
{
    public const ERR = 1;
    /** @var array<Callable> */
    private $listNodeProcessor;

    /** @var ResultCollector  */
    private $resultCollector;

    /** @var EnvironmentDataLoaderInterface  */
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
        EnvironmentDataLoaderInterface $metadataLoader
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

        if ($this->isFalsePositives($val)) {
            return;
        }

        if ($this->globalConfig->getMode() === Config::MODE_MAKE_VENDOR_PRIVATE) {
            $this->withModeVendorPrivate($val, $node);
            return;
        }

        if (true === $this->isComposerNamespace($val)) {
            return;
        }

        if (true === $this->isInPrivateConfiguredEntries($val, $node)) {
            return;
        }
        // echo "\n $val \n ";

        // if ($this->isInPublicConfiguredEntries($val)) {
        //     return;
        // }
    }

    private function isFalsePositives(string $resultTocheck): bool
    {
        $resultTocheck = $this->stripFirstSlash($resultTocheck);

        $result = $resultTocheck;

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

    private function isInPublicConfiguredEntries(string $currentNamespaceAccess): bool
    {
        foreach ($this->globalConfig->getPublicEntries() as $publicEntry) {

            if (strpos($currentNamespaceAccess, $publicEntry) !== false) {
                return true;
            }
        }

        return false;
    }

    private function isInPrivateConfiguredEntries(string $currentNamespaceAccess, Node $node): bool
    {
        foreach ($this->globalConfig->getPrivateEntries() as $privateEntry) {
            if (strpos($currentNamespaceAccess, $privateEntry) !== false) {
                $this->pushError($currentNamespaceAccess, $node);
                return true;
            }
        }
        return false;
    }

    private function pushError(string $val, Node $node): void
    {
        $err = "\t > ERROR Line: " . $node->getLine() . " of use $val " . \PHP_EOL; //todo: output data no context
        $this->resultCollector->addResult(new Result($err, self::ERR));
    }

    private function isComposerNamespace(string $val): bool
    {
        $val = $this->stripFirstSlash($val);
        if ($this->metadataLoader
            ->getCollectComposerNamespace()
            ->booleanSearch($this->matchPos, $val)
        ) {
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

    private function withModeVendorPrivate(string $currentNamespaceAccess, Node $node): void
    {
        if ($this->isInPublicConfiguredEntries($currentNamespaceAccess)) {
            return;
        }

        if (true === $this->isComposerNamespace($currentNamespaceAccess)) {
            return;
        }        

        $this->pushError($currentNamespaceAccess, $node);
        return;
    }

    private function stripFirstSlash(string $token): string
    {
        if ($token[0] === '\\') {
            $token = substr($token, 1, strlen($token));
        }

        return $token;
    }
}
