<?php declare(strict_types=1);

namespace NamespaceProtector\OutputDevice;

use NamespaceProtector\Result\ErrorResult;
use Symfony\Component\Console\Output\OutputInterface;
use NamespaceProtector\Result\ResultProcessorInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;

final class ConsoleDevice implements OutputDeviceInterface
{
    /** @var OutputInterface */
    private $outputInterface;

    /** @var int */
    private $totalErrors;

    public function __construct(OutputInterface $outputInterface)
    {
        $this->totalErrors = 0;
        $this->outputInterface = $outputInterface;
    }

    public function output(ResultProcessorInterface $value): void
    {
        $output = '';
        $this->totalErrors = 0;
        foreach ($value->getProcessedResult() as $processedFileResult) {
            $output .= $this->plot($processedFileResult);
        }

        if ($output === '') {
            echo "\n";
            $this->outputInterface->writeln('');
        }

        echo $output;
        $this->outputInterface->writeln('<fg=blue>Total files: ' . $value->getProcessedResult()->count() . '</>');
        $this->outputInterface->writeln('<fg=red>Total errors: ' . $this->totalErrors . '</>');
    }

    private function plot(ResultProcessedFileInterface $processedFileResult): string
    {
        $resultbuffer = '';
        $resultTitle = '';

        if (\count($processedFileResult->getConflicts()) > 0) {
            $resultTitle = "\nProcessed file: " . $processedFileResult->getFileName() . "\n";
        }

        foreach ($processedFileResult->getConflicts() as $conflict) {
            $resultbuffer .= $this->plotResult($conflict);
            $this->totalErrors++;
        }

        if ($resultbuffer === '') {
            return $resultbuffer;
        }

        return $resultTitle . $resultbuffer;
    }

    private function plotResult(ErrorResult $result): string
    {
        return \Safe\sprintf("\t > ERROR Line: %d of use %s ", $result->getLine(), $result->getUse()) . "\n";
    }
}
