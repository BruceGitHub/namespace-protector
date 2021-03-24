<?php declare(strict_types=1);

namespace Tests\All\Cache;

use Psr\SimpleCache\CacheInterface;
use Tests\All\AbstractUnitTestCase;
use NamespaceProtector\Cache\SimpleFileCache;
use NamespaceProtector\Common\FileSystemPath;

class SimpleFileCacheTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_clear_item(): void
    {
        $simppleFileCache = $this->getSUT();
        $return = $simppleFileCache->clear();
        $this->assertTrue($return);

        $return = $simppleFileCache->get('ast.json');
        $this->assertEquals(null, $return);
    }

    /** @test */
    public function it_delete_item(): void
    {
        $simppleFileCache = $this->getSUT();
        $return = $simppleFileCache->delete('ast.json');
        $this->assertTrue($return);

        $return = $simppleFileCache->get('ast.json');
        $this->assertEquals(null, $return);
    }

    /** @test */
    public function it_set_item(): void
    {
        $simppleFileCache = $this->getSUT();
        $return = $simppleFileCache->set('ast.json', '{"field":"name"}');
        $this->assertTrue($return);

        $json = $simppleFileCache->get('ast.json');
        $this->assertEquals('{"field":"name"}', $json);
    }

    /** @test */
    public function it_get_item_if_exist(): void
    {
        $simppleFileCache = $this->getSUT();
        $json = $simppleFileCache->get('ast.json');

        $this->assertEquals(['name' => 'value'], $json);
    }

    /** @test */
    public function it_get_default_if_not_exist(): void
    {
        $simppleFileCache = $this->getSUT();
        $return = $simppleFileCache->get('astNotExist.json', 'default');

        $this->assertEquals('default', $return);
    }

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
            ->addFile('First.php', 'php', 'files')
            ->addFile('ast.json', 'json', 'files')
            ->buildFileSystemUrl();

        $simppleFileCache = new SimpleFileCache(new FileSystemPath($fileSystem . '/files/'));

        return $simppleFileCache;
    }
}
