<?php

namespace NamespaceProtector\Parser\Node;

use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitor\NameResolver;
use NamespaceProtector\Db\BooleanMatchKey;
use NamespaceProtector\Db\BooleanMatchPos;
use NamespaceProtector\Result\ErrorResult;
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
        $resultProcessNode = $func($node);

        $val = new Entry($resultProcessNode);

        if ($this->isFalsePositives($val)) {
            return;
        }

        if ($this->globalConfig->getMode() === Config::MODE_MAKE_VENDOR_PRIVATE) {
            $this->withModeVendorPrivate($val, $node);
            return;
        }

        if (true === $this->isInConfiguredComposerPsr4Namespaces($val)) {
            return;
        }

        if (true === $this->isInPrivateConfiguredEntries($val, $node)) {
            $this->pushError($val, $node);
            return;
        }
    }

    private function isFalsePositives(Entry $resultTocheck): bool
    {
        $result = $this->stripFirstSlash($resultTocheck);

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

    private function isInPublicConfiguredEntries(Entry $currentNamespaceAccess): bool
    {
        foreach ($this->globalConfig->getPublicEntries() as $publicEntry) {
            $publicEntry = \strtolower($publicEntry);
            $current = \strtolower($currentNamespaceAccess->get());
            if (strpos($current, $publicEntry) !== false) {
                return true;
            }
        }

        return false;
    }

    private function isInPrivateConfiguredEntries(Entry $currentNamespaceAccess, Node $node): bool
    {
        foreach ($this->globalConfig->getPrivateEntries() as $privateEntry) {
            $privateEntry = \strtolower($privateEntry);
            $current = \strtolower($currentNamespaceAccess->get());
            if (strpos($current, $privateEntry) !== false) {
                return true;
            }
        }
        return false;
    }

    private function pushError(Entry $val, Node $node): void
    {
        $err = new ErrorResult($node->getLine(),$val->get(). \PHP_EOL, self::ERR);
        $this->resultCollector->addResult($err);
    }

    private function isInConfiguredComposerPsr4Namespaces(Entry $val): bool
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

    private function valueExist(DbKeyValueInterface $collections, MatchCollectionInterface $matchCriteria, Entry $matchMe): bool
    {
        if ($collections->booleanSearch($matchCriteria, $matchMe)) {
            return true;
        }

        return false;
    }

    private function withModeVendorPrivate(Entry $currentNamespaceAccess, Node $node): void
    {
        if ($this->isInPublicConfiguredEntries($currentNamespaceAccess)) {
            return;
        }

        if (true === $this->isInConfiguredComposerPsr4Namespaces($currentNamespaceAccess)) {
            return;
        }

        $this->pushError($currentNamespaceAccess, $node);
        return;
    }

    private function stripFirstSlash(Entry $token): Entry
    {
        if ($token->get()[0] === '\\') {
            return new Entry(substr($token->get(), 1, strlen($token->get())));
        }

        return $token;
    }
}
