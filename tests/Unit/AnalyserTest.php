<?php

declare(strict_types=1);

namespace Tests\Unit;

use NamespaceProtector\Analyser;
use NamespaceProtector\Result\Result;
use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultAnalyserInterface;

class AnalyserTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $file = $this->getFileToParse();
        $parser = $this->prophesize(ParserInterface::class);

        $parser->parseFile($file)
            ->shouldBeCalled()
            ->willReturn(new ResultParser(new ResultCollected()));

        $parser = $parser->reveal();

        $analyser = $this->createAnalyser($parser);
        $result = $analyser->execute($file);

        $this->assertInstanceOf(ResultAnalyserInterface::class, $result);
    }

    private function createAnalyser($parser): Analyser
    {
        $analyser = new Analyser($parser);
        return $analyser;
    }

    /** @test */
    public function it_parse_file_with_one_error(): void
    {
        $file = $this->getFileToParse();

        $result = [];
        $result[] = new Result('Message', 1);

        $parser = $this->prophesize(ParserInterface::class);
        $parser->parseFile($file)
            ->willReturn(
                new ResultParser(new ResultCollected($result))
            );

        $analyser = $this->createAnalyser($parser->reveal(), $file);
        $result = $analyser->execute($file);

        $this->assertTrue($result->withResults());
        $this->assertEquals(1, $result->count());
    }

    private function getFileToParse()
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('ClassPsr4Composer.php', 'php', 'files')
            ->buildFileSystemUrl();

        $file = new FileSystemPath($fileSystem . '/files/ClassPsr4Composer.php');

        return $file;
    }
}
