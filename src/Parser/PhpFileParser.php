<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser;

use MinimalVo\BaseValueObject\StringVo;
use PhpParser\Parser;
use PhpParser\NodeTraverserInterface;
use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultParserInterface;
use NamespaceProtector\Result\ResultProcessedMutableFile;
use NamespaceProtector\Result\Factory\CollectionFactoryInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;
use NamespaceProtector\Parser\Node\NamespaceProtectorVisitorInterface;

final class PhpFileParser implements ParserInterface
{
    public function __construct(
        private \Psr\SimpleCache\CacheInterface $cache,
        private NodeTraverserInterface $traverser,
        private NamespaceProtectorVisitorInterface $namespaceProtectorVisitor,
        private Parser $parser,
        private CollectionFactoryInterface $collectedFactory
    ) {
        $traverser->addVisitor($namespaceProtectorVisitor);
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

        $processFileResult = new ResultProcessedMutableFile(StringVo::fromValue($pathFile->get()));

        array_map(
            fn ($item) => $processFileResult->addConflic($item),
            iterator_to_array($this->namespaceProtectorVisitor->getStoreProcessedResult()->getIterator())
        );

        $collection = $this->collectedFactory->createMutableCollection([$processFileResult->getReadOnlyProcessedFile()]);

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
