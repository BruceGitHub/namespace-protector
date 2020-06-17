<?php
declare(strict_types=1);

namespace Unit\Common;


use NamespaceProtector\Common\FileSystemPath;
use PHPUnit\Framework\TestCase;

class FileSystemPathTest extends TestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $fileSystemPath = new FileSystemPath('path');

        $this->assertEquals('path',$fileSystemPath->get());
    }
}
