<?php
declare(strict_types=1);

namespace Tests\All\Common;

use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Common\FileSystemPath;
use Tests\All\AbstractUnitTestCase;

class FileSystemPathTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $fs = $this->getVirtualFileSystem();
        $fileSystemPath = new FileSystemPath(StringVo::fromValue($fs->url()));

        $this->assertEquals('vfs://root', $fileSystemPath->get());
    }

    /** @test */
    public function it_invoke_work(): void
    {
        $fs = $this->getVirtualFileSystem();
        $fileSystemPath = new FileSystemPath(StringVo::fromValue($fs->url()));

        $this->assertEquals('vfs://root', $fileSystemPath());
    }
}
