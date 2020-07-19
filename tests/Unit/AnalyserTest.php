<?php declare(strict_types=1);

namespace Tests\Unit;

use NamespaceProtector\Analyser;
use NamespaceProtector\Result\Result;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\OutputDevice\ConsoleDevice;
use NamespaceProtector\Result\ResultCollectorReadable;
use NamespaceProtector\OutputDevice\OutputDeviceInterface;
use NamespaceProtector\Result\ResultParserNamespaceValidate;

class AnalyserTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $file = $this->getFileToParse();
        $parser = $this->prophesize(ParserInterface::class);
        $parser->parseFile($file)
                ->shouldBeCalled()
                ->willReturn(new ResultParserNamespaceValidate())
                ;

        $resultCollector = $this->resultCollectorWithError();

        $parser->getListResult()
                ->shouldBeCalled()
                ->willReturn($resultCollector)
                ;

        $parser = $parser->reveal();

        $analyser = $this->createAnalyser($parser, $file);
        $result = $analyser->execute($file);

        $this->assertInstanceOf(ResultParserNamespaceValidate::class,$result);
    }

    private function createAnalyser($parser, $file): Analyser
    {
        $console = $this->prophesize(OutputDeviceInterface::class);
        $console->output('Message')
                ->shouldBeCalled()
                ;

        $analyser = new Analyser($console->reveal(), $parser);
        return $analyser;
    }

    /** @test */
    public function it_parse_file_with_one_error(): void
    {
        $file = $this->getFileToParse();
        $parser = $this->prophesize(ParserInterface::class);
        $parser->parseFile($file)
                ->shouldBeCalled();

        $resultCollector = $this->resultCollectorWithError();
        $parser->getListResult()
                ->shouldBeCalled()
                ->willReturn(
                    $resultCollector
                );

        $parser = $parser->reveal();

        $analyser = $this->createAnalyser($parser, $file);

        /** @var ResultParserNamespaceValidate $result */
        $result = $analyser->execute($file);

        $this->assertTrue($result->withError());
        $this->assertEquals(1, $result->getCountErrors());
    }

    private function resultCollectorWithError(): ResultCollectorReadable
    {
        $resultCollector = new ResultCollector();
        $resultCollector->addResult(new Result('Message', 1));

        return new ResultCollectorReadable($resultCollector);
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
