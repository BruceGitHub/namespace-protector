<?php

namespace App\Parser\Node;

use App\MetadataLoader;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\Stmt\FullyQualified;

//lib namespace
use App\Result\ResultCollector;
use App\Result\Result;
use App\Config;

final class PhpNode extends NameResolver
{
    public const ERR=1; 
    private $listNodeProcessor = [];
    private $resultCollector;
    private $metadataLoader;
    private $globalConfig; 

    public function __construct(
        Config $configGlobal,
        array $config,
        ResultCollector $resultCollector,
        MetadataLoader $metadataLoader
    ) {
        parent::__construct(null, $config);

        $this->globalConfig = $configGlobal;
        $this->metadataLoader = $metadataLoader;
        $this->resultCollector = $resultCollector;

        $this->listNodeProcessor[UseUse::class] = function (Node $node): string {
            return $node->name->toCodeString();
        };

        $this->listNodeProcessor[FullyQualified::class] = function (Node $node): string {
            return $node->name->toCodeString();
        };
    }

    public function enterNode(Node $node)
    {
        $this->processNode($node);
    }

    private function processNode(Node $node): void
    {
        $class = \get_class($node);
        if (isset($this->listNodeProcessor[$class])) {
            $func = $this->listNodeProcessor[$class];
            $val = $func($node);

            if ($this->isFalsePositives($val)) {
                return;
            }
            
            foreach ($this->globalConfig->getNamespaceToValidate() as $priv) {

                if (strpos($val,$priv)>=1) {
                    $err = "\t > ERROR: of use $val. it's PRIVATE namespace access, on Line: ". 
                            $node->getLine().
                            PHP_EOL
                            ;
                    $this->resultCollector->addResult(new Result($err,self::ERR));
                }
            }
            

        }
    }

    private function isFalsePositives(string $result): bool
    {
        $val = str_replace('\\', '', $result);
        if (\in_array("$val", $this->metadataLoader->getCollectBaseClasses(), true)) {
            return true;
        }

        if (\in_array("$val", $this->metadataLoader->getCollectBaseInterfaces(), true)) {
            return true;
        }

        if (\in_array("$val", $this->metadataLoader->getCollectBaseFunctions(), true)) {
            return true;
        }

        if (\key_exists("$val", $this->metadataLoader->getCollectBaseConstants())) {
            return true;
        }

        return false;
    }
}
