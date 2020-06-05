<?php

namespace NamespaceProtector\Command;

use NamespaceProtector\Config;
use NamespaceProtector\Common\FileSystemPath;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class NamespaceProtectorCommand extends ValidateNamespaceCommand
{
    public function getConfig(): Config
    {
        return   new Config(
            new FileSystemPath('src'),
            [
                '\Legacy\Controller',
            ],
            [
                '\Facile\Pagamenti\Legacy\ClientPagamentiLegacy',
            ],
            Config::MODE_PRIVATE
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $script_start =  $this->startWatch();

        $returnValue = parent::execute($input, $output);

        $elapsed_time = $this->stopWatch($script_start);

        $output->writeln("<fg=green>Elapsed time: ".$elapsed_time.'</>');

        return $returnValue ;
    }

    private function startWatch(): float
    {
        list($usec, $sec) = explode(' ', microtime());
        return  (float)$sec + (float)$usec;
    }

    private function stopWatch(float $script_start): float
    {
        list($usec, $sec) = explode(' ', microtime());
        $script_end = (float)$sec + (float)$usec;

        return  round($script_end - $script_start, 5);
    }
}
