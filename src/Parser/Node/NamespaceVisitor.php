<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;
use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitor\NameResolver;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollected;
use Psr\EventDispatcher\EventDispatcherInterface;
use NamespaceProtector\Result\ResultCollectedReadable;
use NamespaceProtector\Parser\Node\Event\FoundUseNamespace;
use NamespaceProtector\Result\Factory\ErrorCollectionFactoryInterface;

final class NamespaceVisitor extends NameResolver implements NamespaceProtectorVisitorInterface
{
    public const ERR = 1;

    /** @var array<Callable> */
    private array $listNodeProcessor;

    /** @var ResultCollected<ErrorResult> */
    private ResultCollected $storeProcessNodeResult;

    private ErrorCollectionFactoryInterface $errorCollectionFactory;

    /**
     * @param array<string,mixed> $configParser
     */
    public function __construct(
        array $configParser,
        EventDispatcherInterface $eventDispatcher,
        ErrorCollectionFactoryInterface $errorCollectionFactory
    ) {
        parent::__construct(null, $configParser);
        $this->listNodeProcessor = [];
        $this->errorCollectionFactory = $errorCollectionFactory;
        $this->configure($eventDispatcher);
    }

    private function configure(EventDispatcherInterface $eventDispatcher): void
    {
        $this->listNodeProcessor[UseUse::class] = function (Node $node) use ($eventDispatcher): object {
            /** @var UseUse $node */
            return $eventDispatcher->dispatch(new FoundUseNamespace(IntegerVo::fromValue($node->getStartLine()), StringVo::fromValue($node->name->toCodeString())));
        };

        $this->listNodeProcessor[FullyQualified::class] = function (Node $node) use ($eventDispatcher): object {
            /** @var FullyQualified $node */
            return $eventDispatcher->dispatch(new FoundUseNamespace(IntegerVo::fromValue($node->getStartLine()), StringVo::fromValue($node->toCodeString())));
        };

        $collection = $this->errorCollectionFactory->createForErrorResult();

        $this->storeProcessNodeResult = $collection;
    }

    public function enterNode(Node $node) //todo miss type hint (override fro lib)
    {
        $this->processNode($node);
        return $node;
    }

    public function beforeTraverse(array $nodes)
    { //todo miss type hint (override from lib)
        $this->storeProcessNodeResult->emptyResult();
        parent::beforeTraverse($nodes);
        return null;
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

        if (!$resultProcessNode->withError()->toValue()) {
            return;
        }

        //todo: not now
        // $additionalInformation = '';
        // if ($resultProcessNode->getAdditionalInformation() !== '') {
        //     $additionalInformation = '( ' . $resultProcessNode->getAdditionalInformation() . ' )';
        // }

        $err = new ErrorResult(
            IntegerVo::fromValue($node->getLine()),
            StringVo::fromValue($resultProcessNode->getNodeName()->toValue()),
            IntegerVo::fromValue(self::ERR)
        );

        $this->storeProcessNodeResult->addResult($err);
    }

    /**
     * @return ResultCollectedReadable<ErrorResult>
     */
    public function getStoreProcessedResult(): ResultCollectedReadable
    {
        return new ResultCollectedReadable($this->storeProcessNodeResult);
    }
}
