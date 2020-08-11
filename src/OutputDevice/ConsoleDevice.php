<?php declare(strict_types=1);

namespace NamespaceProtector\OutputDevice;

use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultInterface;
use NamespaceProtector\Result\ResultProcessedFile;
use Symfony\Component\Console\Output\OutputInterface;
use NamespaceProtector\Result\ResultProcessorInterface;

final class ConsoleDevice implements OutputDeviceInterface
{
    /** @var OutputInterface */
    private $outputInterface;

    /** @var int */
    private $totalErrors;

    public function __construct(OutputInterface $outputInterface)
    {
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
            $this->outputInterface->writeln('<fg=blue>No output</>');
        }

        echo $output;
        $this->outputInterface->writeln('<fg=red>Total files: ' . $value->getProcessedResult()->count() . '</>');
        $this->outputInterface->writeln('<fg=red>Total errors: ' . $this->totalErrors . '</>');
    }

    public function plot(ResultProcessedFile $processedFileResult): string
    {
        $resultbuffer = '';
        $resultTitle = "\nProcessed file: " . $processedFileResult->get() . "\n";

        foreach ($processedFileResult->getConflicts() as $conflict) {
            $resultbuffer .= $this->plotResult($conflict);
            $this->totalErrors++;
        }

        if ($resultbuffer === '') {
            return $resultbuffer;
        }

        return $resultTitle . $resultbuffer;
    }

    public function plotResult(ResultInterface $result): string
    {
        if ($result instanceof ErrorResult) {
            return \Safe\sprintf("\t > ERROR Line: %d of use %s ", $result->getLine(), $result->getUse()) . "\n";
        }

        return $result->get();
    }
}
