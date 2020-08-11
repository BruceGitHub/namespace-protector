<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitor\NameResolver;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollector;
use Psr\EventDispatcher\EventDispatcherInterface;
use NamespaceProtector\Parser\Node\Event\FoundUseNamespace;
use NamespaceProtector\Result\ResultCollectorReadable;

final class NamespaceVisitor extends NameResolver implements NamespaceProtectorVisitorInterface
{
    public const ERR = 1;

    /** @var array<Callable> */
    private $listNodeProcessor;

    /** @var ResultCollector */
    private $storeProcessNodeResult;

    /**
     * @param array<string,mixed> $configParser
     */
    public function __construct(
        array $configParser,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct(null, $configParser);
        $this->configure($eventDispatcher);
    }

    private function configure(EventDispatcherInterface $eventDispatcher): void
    {
        $this->listNodeProcessor[UseUse::class] = static function (Node $node) use ($eventDispatcher) {
            /** @var UseUse $node */
            return $eventDispatcher->dispatch(new FoundUseNamespace($node->getStartLine(), $node->name->toCodeString()));
        };

        $this->listNodeProcessor[FullyQualified::class] = static function (Node $node) use ($eventDispatcher) {
            /** @var FullyQualified $node */
            return $eventDispatcher->dispatch(new FoundUseNamespace($node->getStartLine(), $node->toCodeString()));
        };

        $this->storeProcessNodeResult = new ResultCollector();
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

        /** @var FoundUseNamespace */
        $resultProcessNode = $func($node);

        if (!$resultProcessNode->withError()) {
            return;
        }

        $additionalInformation = '';
        if ($resultProcessNode->getAdditionalInformation() !== '') {
            $additionalInformation = '( ' . $resultProcessNode->getAdditionalInformation() . ' )';
        }

        $err = new ErrorResult(
            $node->getLine(),
            $resultProcessNode->getNodeName(),
            self::ERR
        );

        $this->storeProcessNodeResult->addResult($err);
    }

    public function getStoreProcessNodeResult(): ResultCollectorReadable
    {
        return new ResultCollectorReadable($this->storeProcessNodeResult);
    }
}
