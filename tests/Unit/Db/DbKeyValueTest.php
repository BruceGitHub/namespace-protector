<?php declare(strict_types=1);

namespace Tests\Unit\Db;

use NamespaceProtector\Entry\Entry;
use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\Db\MatchCollectionInterface;

class DbKeyValueTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_work(): void
    {
        $db = new DbKeyValue(['barr' => 'foo']);

        $this->assertEquals(1, $db->count());
    }

    /** @test */
    public function it_add_work(): void
    {
        $db = new DbKeyValue(['barr' => 'foo']);
        $db->add('foo2', 'bar2');

        $this->assertEquals(2, $db->count());
    }

    /** @test */
    public function it_boolean_search_work(): void
    {
        $collections = ['barr' => 'foo'];
        $entry = new Entry('matchMe');

        $machCriteria = $this->prophesize(MatchCollectionInterface::class);
        $machCriteria->evaluate($collections, $entry)
                      ->shouldBeCalled()
                      ->willReturn(true);

        $db = new DbKeyValue($collections);

        $result = $db->booleanSearch($machCriteria->reveal(), $entry);
        $this->assertEquals(1, $db->count());
        $this->assertTrue($result);
    }
}
