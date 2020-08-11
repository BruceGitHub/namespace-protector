<?php declare(strict_types=1);

namespace Tests\Unit;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use NamespaceProtector\Result\ResultProcessedFile;
use NamespaceProtector\Result\ResultCollectorReadable;

abstract class AbstractUnitTestCase extends TestCase
{
    use ProphecyTrait;

    protected function getVirtualFileSystem()
    {
        $directory = [
            'json' => [
                'composer.json' => \file_get_contents(__DIR__ . '/../Stub/json/composer.json'),
                'namespace-protector-config.json' => \file_get_contents(__DIR__ . '/../Stub/json/namespace-protector-config.json'),
                'namespace-protector-config-mod-public.json' => \file_get_contents(__DIR__ . '/../Stub/json/namespace-protector-config-mod-public.json'),
            ],
            'files' => [
                'First.php' => \file_get_contents(__DIR__ . '/../Stub/php/First.php'),
                'no_violation.php' => \file_get_contents(__DIR__ . '/../Stub/php/no_violation.php'),
                'ClassPsr4Composer.php' => \file_get_contents(__DIR__ . '/../Stub/php/ClassPsr4Composer.php'),
                'FileThatUsePrivateNamespace.php' => \file_get_contents(__DIR__ . '/../Stub/php/FileThatUsePrivateNamespace.php'),
            ],
        ];

        return vfsStream::setup('root', 777, $directory);
    }

    //builder todo: move in specific class
    private $fileSystemtoBuild;

    protected function StartBuildFileSystem(): self
    {
        $this->fileSystemtoBuild = [];
        return $this;
    }

    protected function addDirectory(string $directory): self
    {
        $this->fileSystemtoBuild[$directory] = [];

        return $this;
    }

    protected function addFile(string $pathFile, string $directoryReal = '', string $directoryVirtual): self
    {
        $this->fileSystemtoBuild[$directoryVirtual][$pathFile] = \file_get_contents(__DIR__ . '/../Stub/' . $directoryReal . '/' . $pathFile);

        return $this;
    }

    protected function addFileWithCallable(string $pathFile, string $directoryReal = '', string $directoryVirtual, callable $callable): self
    {
        $this->fileSystemtoBuild[$directoryVirtual][$pathFile] = $callable($directoryReal, $pathFile);
        return $this;
    }

    protected function buildFileSystem()
    {
        return vfsStream::setup('root', 777, $this->fileSystemtoBuild);
    }

    protected function buildFileSystemUrl()
    {
        return vfsStream::setup('root', 777, $this->fileSystemtoBuild)->url();
    }

    //builder todo: move in specific class

    public function assertContainsProcessFile(string $expected, ResultCollectorReadable $iterable): void
    {
        $iterable->getIterator()->rewind();

        /** @var ResultProcessedFile $item */
        foreach ($iterable as $item) {
            $this->assertStringContainsString($expected, $item->get());
        }
    }

    public function assertContainsConflicts(string $expected, ResultCollectorReadable $iterable): void
    {
        $iterable->getIterator()->rewind();

        /** @var ResultProcessedFile $item */
        foreach ($iterable as $item) {
            $arr = [];
            foreach ($item->getConflicts() as $c) {
                $arr[] = $c->get();
            }

            $this->assertContains($expected, $arr);
        }
    }
}
