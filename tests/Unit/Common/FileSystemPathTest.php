<?php
declare(strict_types=1);

namespace Unit\Common;

use NamespaceProtector\Common\FileSystemPath;
use Tests\Unit\AbstractUnitTestCase;

class FileSystemPathTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $fs = $this->getVirtualFileSystem();
        $fileSystemPath = new FileSystemPath($fs->url());

        $this->assertEquals('vfs://root', $fileSystemPath->get());
    }

    /** @test */
    public function it_invoke_work(): void
    {
        $fs = $this->getVirtualFileSystem();
        $fileSystemPath = new FileSystemPath($fs->url());

        $this->assertEquals('vfs://root', $fileSystemPath());
    }
}
