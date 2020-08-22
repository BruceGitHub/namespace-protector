<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser;

use PhpParser\Parser;
use PhpParser\NodeTraverserInterface;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultCollected;
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
        $nodeTraverserInterface->addVisitor($visitor);
    }

    public function parseFile(PathInterface $pathFile): ResultParserInterface
    {
        $this->pathFileToParse = $pathFile;

        $ast = $this->fetchAstAfterParse($pathFile);
        $this->namespaceProtectorVisitor->clearStoredProcessedResult();

        $this->traverser->traverse($ast);

        return $this->getListResult();
    }

    private function getListResult(): ResultParserInterface
    {
        if (\count($this->namespaceProtectorVisitor->getStoreProcessedResult()) === 0) {
            return new ResultParser();
        }

        $processFileResult = new ResultProcessedFile($this->pathFileToParse->get());

        /** @var ErrorResult $singleConflict */
        foreach ($this->namespaceProtectorVisitor->getStoreProcessedResult() as $singleConflict) {
            $processFileResult->addConflic($singleConflict);
        }

        /** @var ResultCollectedReadable<ResultProcessorInterface> */
        $collected = new ResultCollectedReadable(new ResultCollected([$processFileResult]));
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
