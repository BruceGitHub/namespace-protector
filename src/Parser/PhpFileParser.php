<?php declare(strict_types=1);

namespace NamespaceProtector\Parser;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Result\Result;
use NamespaceProtector\Parser\Node\PhpNode;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultCollector;
use Psr\EventDispatcher\EventDispatcherInterface;
use NamespaceProtector\Result\ResultCollectorReadable;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;

final class PhpFileParser implements ParserInterface
{
    private const ONLY_ONE_ENTRY = 1;

    /** @var \PhpParser\Parser  */
    private $parser;

    /** @var NodeTraverser  */
    private $traverser;

    /** @var ResultCollector  */
    private $resultCollector;

    /** @var \Psr\SimpleCache\CacheInterface  */
    private $cache;

    public function __construct(
        Config $config,
        \Psr\SimpleCache\CacheInterface $cache,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser();

        $this->resultCollector = new ResultCollector();

        $phpNode = new PhpNode(
            $config,
            ['preserveOriginalNames' => true, 'replaceNodes' => true],
            $this->resultCollector,
            $eventDispatcher
        );

        $this->traverser->addVisitor($phpNode);
        $this->cache = $cache;
    }

    public function parseFile(PathInterface $pathFile): void
    {
        $this->resultCollector->emptyResult();
        $this->resultCollector->addResult(new Result('Process file: ' . $pathFile->get() . PHP_EOL));

        $ast = $this->fetchAst($pathFile);

        $this->traverser->traverse($ast);

        $this->emptyLogIfNoErrorEntry();
    }

    public function getListResult(): ResultCollectorReadable
    {
        return new ResultCollectorReadable($this->resultCollector);
    }

    private function emptyLogIfNoErrorEntry(): void
    {
        if (\count($this->resultCollector->get()) === self::ONLY_ONE_ENTRY) {
            $this->resultCollector->emptyResult();
        }
    }

    /**
     * @return array<mixed>
     */
    private function fetchAst(PathInterface $pathFile): array
    {
        $code = $pathFile->get();
        $keyEntryForCache = sha1($code) . '.' . base64_encode($pathFile->get());

        if (!$this->cache->has($keyEntryForCache)) {
            $code = \file_get_contents($pathFile->get());
            if ($code === false) {
                throw new \RuntimeException(NamespaceProtectorExceptionInterface::MSG_PLAIN_ERROR_FILE_GET_CONTENT);
            }

            $ast = $this->parser->parse($code);
            $this->cache->set($keyEntryForCache, $ast);

            return $ast ?? [];
        }

        return $this->cache->get($keyEntryForCache, []);
    }
}
