<?php

namespace App\Parser;

use PhpParser\Parser;
use PhpParser\NameContext;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\Node\Name\FullyQualified;

//lib namespace
use App\Common\PathInterface;
use App\Parser\Node\PhpNode;
use App\Result\ResultCollector;
use App\Result\Result;
use App\MetadataLoader;
use App\Config;

final class PhpFileParser implements ParserInteface
{
    private const ONLY_ONE_ENTRY=1; 
    private $parser;
    private $traverser;
    private $phpNode;
    private $resultCollector;

    public function __construct(Config $config, MetadataLoader $metadataLoader)
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser();
        $this->resultCollector = new ResultCollector();

        $this->phpNode = new PhpNode(
            $config,
            ['preserveOriginalNames' => true, 'replaceNodes' => true],
            $this->resultCollector,
            $metadataLoader
        );

        $this->traverser->addVisitor($this->phpNode);
    }

    public function parseFile(PathInterface $pathFile): void
    {
        $this->resultCollector->empyResult();
        $this->resultCollector->addResult(
            new Result('Process file: ' . $pathFile->get() . PHP_EOL)
        );

        $code = file_get_contents($pathFile->get());
        $ast = $this->parser->parse($code);

        $this->traverser->traverse($ast);

        if (\count($this->resultCollector->get()) === self::ONLY_ONE_ENTRY) {
            $this->resultCollector->empyResult();
        }
    }

    public function getListResult(): ResultCollector
    {
        return $this->resultCollector;
    }
}
