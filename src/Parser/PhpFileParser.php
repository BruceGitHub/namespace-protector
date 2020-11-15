<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser;

use PhpParser\Parser;
use PhpParser\NodeTraverserInterface;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultParserInterface;
use NamespaceProtector\Result\ResultProcessedMutableFile;
use NamespaceProtector\Result\Factory\CollectionFactoryInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;
use NamespaceProtector\Parser\Node\NamespaceProtectorVisitorInterface;

final class PhpFileParser implements ParserInterface
{
    /** @var \PhpParser\Parser  */
    private $parser;

    /** @var \PhpParser\NodeTraverserInterface.  */
    private $traverser;

    /** @var \Psr\SimpleCache\CacheInterface  */
    private $cache;

    /** @var NamespaceProtectorVisitorInterface */
    private $namespaceProtectorVisitor;

    /** @var PathInterface */
    private $pathFileToParse;

    /** @var CollectionFactoryInterface */
    private $collectedFactory;

    public function __construct(
        \Psr\SimpleCache\CacheInterface $cache,
        NodeTraverserInterface $nodeTraverserInterface,
        NamespaceProtectorVisitorInterface $visitor,
        Parser $parser,
        CollectionFactoryInterface $collectedFactory
    ) {
        $this->cache = $cache;
        $this->traverser = $nodeTraverserInterface;
        $this->parser = $parser;
        $this->namespaceProtectorVisitor = $visitor;
        $nodeTraverserInterface->addVisitor($visitor);
        $this->collectedFactory = $collectedFactory;
    }

    public function parseFile(PathInterface $pathFile): ResultParserInterface
    {
        $this->pathFileToParse = $pathFile;

        $ast = $this->fetchAstAfterParse($pathFile);
        $this->traverser->traverse($ast);

        return $this->getListResult();
    }

    private function getListResult(): ResultParserInterface
    {
        if (\count($this->namespaceProtectorVisitor->getStoreProcessedResult()) === 0) {
            $collection = $this->collectedFactory->createEmptyMutableCollection();
            return new ResultParser($collection);
        }

        $processFileResult = new ResultProcessedMutableFile($this->pathFileToParse->get());

        /** @var ErrorResult $singleConflict */
        foreach ($this->namespaceProtectorVisitor->getStoreProcessedResult() as $singleConflict) {
            $processFileResult->addConflic($singleConflict);
        }
        $collection = $this->collectedFactory->createMutableCollection([$processFileResult]);

        return new ResultParser($collection); //todo: readonly ?
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
