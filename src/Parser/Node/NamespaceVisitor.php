<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitor\NameResolver;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\Parser\Node\Event\FoundUseNamespace;
use Psr\EventDispatcher\EventDispatcherInterface;

final class NamespaceVisitor extends NameResolver
{
    public const ERR = 1;

    /** @var array<Callable> */
    private $listNodeProcessor;

    /** @var ResultCollector  */
    private $resultCollector;

    /**
     * @param array<string,mixed> $configParser
     */
    public function __construct(
        array $configParser,
        ResultCollector $resultCollector,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct(null, $configParser);

        $this->resultCollector = $resultCollector;
        $this->configureEvents($eventDispatcher);
    }

    private function configureEvents(EventDispatcherInterface $eventDispatcher): void
    {
        $this->listNodeProcessor[UseUse::class] = static function (Node $node) use ($eventDispatcher) {
            /** @var UseUse $node */
            return $eventDispatcher->dispatch(new FoundUseNamespace($node->getStartLine(), $node->name->toCodeString()));
        };

        $this->listNodeProcessor[FullyQualified::class] = static function (Node $node) use ($eventDispatcher) {
            /** @var FullyQualified $node */
            return $eventDispatcher->dispatch(new FoundUseNamespace($node->getStartLine(), $node->toCodeString()));
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
            $resultProcessNode->getNodeName() . $additionalInformation . \PHP_EOL,
            self::ERR
        );

        $this->resultCollector->addResult($err);
    }
}
