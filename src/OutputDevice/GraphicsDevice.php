<?php

declare(strict_types=1);

namespace NamespaceProtector\OutputDevice;

use NamespaceProtector\Result\ResultProcessedFileInterface;
use NamespaceProtector\Result\ResultProcessorInterface;

final class GraphicsDevice implements OutputDeviceInterface
{
    public function output(ResultProcessorInterface $value): void
    {
        $graph = new \Fhaculty\Graph\Graph();
        $graph->setAttribute('graphviz.name', 'G');
        $graph->setAttribute('graphviz.graph.rankdir', 'LR');

        array_map(
            fn ($processedFileResult) => $this->plot($graph, $processedFileResult),
            iterator_to_array($value->getProcessedResult()->getIterator())
        );

        $graphviz = new \Graphp\GraphViz\GraphViz();
        $graphviz->display($graph);
    }

    public function plot(\Fhaculty\Graph\Graph $graph, ResultProcessedFileInterface $processedFileResult): void
    {
        if (\count($processedFileResult->getConflicts()) > 0) {
            /** @var int $fileName */
            $fileName = $processedFileResult->getFileName();

            $blue = $graph->createVertex($fileName, true);
            $blue->setAttribute('graphviz.color', 'blue');

            array_map(
                function ($conflict) use ($graph, $blue): void {
                    /** @var int $fileName */
                    $fileName = $conflict->get();

                    $red = $graph->createVertex($fileName, true);
                    $red->setAttribute('graphviz.color', 'red');

                    $edge = $blue->createEdgeTo($red);
                    $edge->setAttribute('graphviz.color', 'grey');
                },
                $processedFileResult->getConflicts()
            );
        }
    }
}
