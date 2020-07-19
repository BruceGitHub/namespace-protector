<?php declare(strict_types=1);

namespace NamespaceProtector\Parser;

use PhpParser\Parser;
use NamespaceProtector\Result\Result;
use PhpParser\NodeTraverserInterface;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\Result\ResultParserInterface;
use NamespaceProtector\Result\ResultCollectorReadable;
use NamespaceProtector\Result\ResultParserNamespaceValidate;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;

final class PhpFileParser implements ParserInterface
{
    private const ONLY_ONE_ENTRY = 1;

    /** @var \PhpParser\Parser  */
    private $parser;

    /** @var \PhpParser\NodeTraverserInterface.  */
    private $traverser;

    /** @var ResultCollector  */
    private $resultCollector;

    /** @var \Psr\SimpleCache\CacheInterface  */
    private $cache;

    public function __construct(
        \Psr\SimpleCache\CacheInterface $cache,
        NodeTraverserInterface $nodeTraverserInterface,
        Parser $parser,
        ResultCollector $resultCollector
    ) {
        $this->cache = $cache;
        $this->traverser = $nodeTraverserInterface;
        $this->resultCollector = $resultCollector;
        $this->parser = $parser;
    }

    public function parseFile(PathInterface $pathFile): ResultParserInterface
    {
        $this->resultCollector->emptyResult();
        $this->resultCollector->addResult(new Result('Process file: ' . $pathFile() . PHP_EOL));

        $ast = $this->fetchAstAfterParse($pathFile);

        $this->traverser->traverse($ast);

        $this->emptyLogIfNoErrorEntry(); //todo: specific purpose

        return new ResultParserNamespaceValidate(); 
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
