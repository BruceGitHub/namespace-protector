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
use NamespaceProtector\Result\ResultProcessedFileEmpty;
use NamespaceProtector\Result\ResultProcessorInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;
use NamespaceProtector\Parser\Node\NamespaceProtectorVisitorInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;

final class PhpFileParser implements ParserInterface
{
    /** @var \PhpParser\Parser  */
    private $parser;

    /** @var \PhpParser\NodeTraverserInterface.  */
    private $traverser;

    /** @var ResultProcessedFileInterface */
    private $processFileResult;

    /** @var \Psr\SimpleCache\CacheInterface  */
    private $cache;

    /** @var NamespaceProtectorVisitorInterface */
    private $namespaceProtectorVisitor;

    /** @var PathInterface */
    private $pathFileToParse;

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
        $this->processFileResult = new ResultProcessedFileEmpty();
        $nodeTraverserInterface->addVisitor($visitor);
    }

    public function parseFile(PathInterface $pathFile): void
    {
        $this->pathFileToParse = $pathFile;

        $ast = $this->fetchAstAfterParse($pathFile);
        $this->namespaceProtectorVisitor->clearStoredProcessedResult();

        $this->traverser->traverse($ast);
    }

    public function getListResult(): ResultParserInterface
    {
        if (\count($this->namespaceProtectorVisitor->getStoreProcessedResult()) === 0) {
            return new ResultParser();
        }

        $this->processFileResult = new ResultProcessedFile($this->pathFileToParse->get());

        /** @var ResultInterface $singleConflict */
        foreach ($this->namespaceProtectorVisitor->getStoreProcessedResult() as $singleConflict) {
            $this->processFileResult->addConflic($singleConflict);
        }

        /** @var ResultCollectedReadable<ResultProcessorInterface> */
        $collected = new ResultCollectedReadable(new ResultCollected([$this->processFileResult]));
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
