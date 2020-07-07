<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitor\NameResolver;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Event\EventDispatcher;
use NamespaceProtector\Event\ListenerProvider;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\Node\Event\FoundUseNamespace;

final class PhpNode extends NameResolver
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
        Config $configGlobal,
        array $configParser,
        ResultCollector $resultCollector,
        EnvironmentDataLoaderInterface $metadataLoader
    ) {
        parent::__construct(null, $configParser);

        $this->resultCollector = $resultCollector;

        $listener = new ListenerProvider();
        $callableUseStatement = new ProcessUseStatement($metadataLoader, $configGlobal);
        $listener->addEventListener(FoundUseNamespace::class, $callableUseStatement);
        $dispatcher = new EventDispatcher($listener);

        $this->listNodeProcessor[UseUse::class] = static function (Node $node) use ($dispatcher) {
            /** @var UseUse $node */
            return $dispatcher->dispatch(new FoundUseNamespace($node->getStartLine(), $node->name->toCodeString()));
        };

        $this->listNodeProcessor[FullyQualified::class] = static function (Node $node) use ($dispatcher) {
            /** @var FullyQualified $node */
            return $dispatcher->dispatch(new FoundUseNamespace($node->getStartLine(), $node->toCodeString()));
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

        if ($resultProcessNode->withError()) {
            $val = new Entry($resultProcessNode->getNodeName());
            $this->pushError($val, $node);
            return;
        }
    }

    private function pushError(Entry $val, Node $node): void
    {
        $err = new ErrorResult($node->getLine(), $val->get() . \PHP_EOL, self::ERR);
        $this->resultCollector->addResult($err);
    }
}
