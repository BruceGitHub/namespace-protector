<?php

namespace Tests\Unit\Cache;

use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Cache\NullCache;

class NullCacheTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_get_key_work(): void
    {
        $nullCache = new NullCache();
        $result = $nullCache->get('value', 'default');
        $this->assertEquals('default', $result);
    }

    /** @test */
    public function it_set_key_work(): void
    {
        $nullCache = new NullCache();
        $result = $nullCache->set('value', 'default');
        $this->assertFalse($result);
    }

    /** @test */
    public function it_delete_work(): void
    {
        $nullCache = new NullCache();
        $result = $nullCache->delete('value');
        $this->assertFalse($result);
    }

    /** @test */
    public function it_clear_work(): void
    {
        $nullCache = new NullCache();
        $result = $nullCache->clear();
        $this->assertFalse($result);
    }

    /** @test */
    public function it_get_multiple_work(): void
    {
        $nullCache = new NullCache();
        $result = $nullCache->getMultiple([], 'default');
        $this->assertEquals([], $result);
    }

    /** @test */
    public function it_set_multiple_work(): void
    {
        $nullCache = new NullCache();
        $result = $nullCache->setMultiple([]);
        $this->assertFalse($result);
    }

    /** @test */
    public function it_delete_multiple_work(): void
    {
        $nullCache = new NullCache();
        $result = $nullCache->deleteMultiple([]);
        $this->assertFalse($result);
    }

    /** @test */
    public function it_has_work(): void
    {
        $nullCache = new NullCache();
        $result = $nullCache->has('value');
        $this->assertFalse($result);
    }
}
