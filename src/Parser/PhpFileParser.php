<?php

namespace NamespaceProtector\Parser;

//lib namespace
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Config;
use NamespaceProtector\EnvironmentDataLoader;
use NamespaceProtector\Parser\Node\PhpNode;
use NamespaceProtector\Result\Result;
use NamespaceProtector\Result\ResultCollector;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;



final class PhpFileParser implements ParserInterface
{
    private const ONLY_ONE_ENTRY=1; 
    private $parser;
    private $traverser;
    private $resultCollector;

    public function __construct(Config $config, EnvironmentDataLoader $metadataLoader)
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser();
        $this->resultCollector = new ResultCollector();

        $phpNode = new PhpNode(
            $config,
            ['preserveOriginalNames' => true, 'replaceNodes' => true],
            $this->resultCollector,
            $metadataLoader
        );

        $this->traverser->addVisitor($phpNode);
    }

    public function parseFile(PathInterface $pathFile): void
    {
        $this->resultCollector->emptyResult();
        $this->resultCollector->addResult(
            new Result('Process file: ' . $pathFile->get() . PHP_EOL)
        );

        $code = file_get_contents($pathFile->get());
        $ast = $this->parser->parse($code);

        $this->traverser->traverse($ast);

        if (\count($this->resultCollector->get()) === self::ONLY_ONE_ENTRY) {
            $this->resultCollector->emptyResult();
        }
    }

    public function getListResult(): ResultCollector
    {
        return $this->resultCollector;
    }
}
