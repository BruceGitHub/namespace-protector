<?php

namespace Tests\Unit\Cache;

use RuntimeException;
use Psr\SimpleCache\CacheInterface;
use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Cache\SimpleFileCache;
use NamespaceProtector\Common\FileSystemPath;

class SimpleFileCacheTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_get_multiple_raise_exception(): void
    {
        $simppleFileCache = $this->getSUT();
        $this->expectException(\RuntimeException::class);
        $simppleFileCache->getMultiple('key');
    }

    /** @test */
    public function it_set_multiple_raise_exception(): void
    {
        $simppleFileCache = $this->getSUT();
        $this->expectException(\RuntimeException::class);
        $simppleFileCache->setMultiple('key');
    }

    /** @test */
    public function it_delete_multiple_raise_exception(): void
    {
        $simppleFileCache = $this->getSUT();
        $this->expectException(\RuntimeException::class);
        $simppleFileCache->deleteMultiple('key');
    }

    private function getSUT(): CacheInterface
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('first.php', 'php', 'files')
            ->buildFileSystemUrl();


        $simppleFileCache = new SimpleFileCache(new FileSystemPath($fileSystem . '/files'));

        return $simppleFileCache;
    }
}
