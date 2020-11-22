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

    /** @var \PhpParser\NodeTraverserInterface  */
    private $traverser;

    /** @var \Psr\SimpleCache\CacheInterface  */
    private $cache;

    /** @var NamespaceProtectorVisitorInterface */
    private $namespaceProtectorVisitor;

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
        $ast = $this->fetchAstAfterParse($pathFile);
        $this->traverser->traverse($ast);

        return $this->getListResult($pathFile);
    }

    private function getListResult(PathInterface $pathFile): ResultParserInterface
    {
        if (\count($this->namespaceProtectorVisitor->getStoreProcessedResult()) === 0) {
            $emptyCollection = $this->collectedFactory->createEmptyMutableCollection();

            return new ResultParser($emptyCollection);
        }

        $processFileResult = new ResultProcessedMutableFile($pathFile->get());

        /** @var ErrorResult $singleConflict */
        foreach ($this->namespaceProtectorVisitor->getStoreProcessedResult() as $singleConflict) {
            $processFileResult->addConflic($singleConflict);
        }

        $collection = $this->collectedFactory->createMutableCollection(
            [
                $processFileResult->getReadOnlyProcessedFile(),
            ]
        );

        return new ResultParser($collection);
    }

    /**
     * @return \PhpParser\Node[]
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

            /**
             * @var \PhpParser\Node[]
             */
            return $ast ?? [];
        }

        /**
         * @var \PhpParser\Node[]
         */
        return $this->cache->get($keyEntryForCache, []);
    }
}
