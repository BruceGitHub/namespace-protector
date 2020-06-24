<?php

namespace Tests\Unit\Scanner;

use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Common\FileSystemPath;

class ComposerJsonTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $composerJson = $this->getComposerJsonPath();
        $composerJsonScanner = new ComposerJson($composerJson);
        $composerJsonScanner->load();

        $this->assertCount(2, $composerJsonScanner->getPsr4Ns());
        $nsKey = \array_keys($composerJsonScanner->getPsr4Ns());
        $this->assertEquals('NamespaceProtector\\', $nsKey[0]);
        $this->assertEquals('NamespaceProtectorClone\\', $nsKey[1]);
    }

    public function it_detect_composer_directory_work(): void
    {
        $composerJson = $this->getComposerJsonPath();
        ComposerJson::detectComposerJsonDirectory();

        $this->markTestIncomplete();
    }

    private function getComposerJsonPath()
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('composer.json', 'json', 'files')
            ->buildFileSystemUrl();

        $path = new FileSystemPath($fileSystem . '/files');


        return $path;
    }
}
