<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser;

use PhpParser\Parser;
use PhpParser\NodeTraverserInterface;
use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultInterface;
use NamespaceProtector\Result\ResultProcessedFile;
use NamespaceProtector\Result\ResultParserInterface;
use NamespaceProtector\Result\ResultCollectedReadable;
use NamespaceProtector\Result\ResultProcessorInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;
use NamespaceProtector\Parser\Node\NamespaceProtectorVisitorInterface;

final class PhpFileParser implements ParserInterface
{
    /** @var \PhpParser\Parser  */
    private $parser;

    /** @var \PhpParser\NodeTraverserInterface.  */
    private $traverser;

    /** @var ResultCollected  */
    private $resultCollector;

    /** @var \Psr\SimpleCache\CacheInterface  */
    private $cache;

    /** @var NamespaceProtectorVisitorInterface */
    private $namespaceProtectorVisitor;

    public function __construct(
        \Psr\SimpleCache\CacheInterface $cache,
        NodeTraverserInterface $nodeTraverserInterface,
        NamespaceProtectorVisitorInterface $visitor,
        Parser $parser
    ) {
        $this->cache = $cache;
        $this->traverser = $nodeTraverserInterface;
        $this->parser = $parser;
        $this->namespaceProtectorVisitor = $visitor;
        $this->resultCollector = $this->createResultCollector();
        $nodeTraverserInterface->addVisitor($visitor);
    }

    /**
     * @return ResultCollected
     */
    private function createResultCollector(): ResultCollected
    {
        return new ResultCollected();
    }

    public function parseFile(PathInterface $pathFile): void
    {
        $this->resultCollector = $this->createResultCollector();

        $ast = $this->fetchAstAfterParse($pathFile);
        $this->traverser->traverse($ast);

        $visitorCollectorResult = $this->namespaceProtectorVisitor->getStoreProcessNodeResult();
        $processFileResult = new ResultProcessedFile($pathFile());

        /** @var ResultInterface $singleResult */
        foreach ($visitorCollectorResult as $singleResult) {
            $processFileResult->add($singleResult);
        }

        $this->resultCollector->addResult($processFileResult);
    }

    public function getListResult(): ResultParserInterface
    {
        /** @var ResultCollectedReadable<ResultProcessorInterface> */
        $collected = new ResultCollectedReadable($this->resultCollector);
        return new ResultParser($collected);
    }

    /**
     * @return array<mixed>
     */
    private function fetchAstAfterParse(PathInterface $pathFile): array
    {
        $code = $pathFile();
        $keyEntryForCache = sha1($code) . '.' . base64_encode($pathFile());

        if (!$this->cache->has($keyEntryForCache)) {
            $code = \file_get_contents($pathFile());
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
