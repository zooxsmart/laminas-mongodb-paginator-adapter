<?php

declare(strict_types=1);

namespace MariojrrcTest\Laminas\Paginator;

use Mariojrrc\Laminas\Paginator\Exception\InvalidClass;
use Mariojrrc\Laminas\Paginator\MongoDBAdapter;
use MariojrrcTest\Laminas\Paginator\Mock\EntityClass;
use MongoDB\Collection;
use MongoDB\Driver\Query;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class MongoDBAdapterTest extends TestCase
{
    protected function setUp() : void
    {
    }

    public function testConstructSuccess()
    {
        $collection = $this->prophesize(Collection::class);
        $adapter = new MongoDBAdapter($collection->reveal());
        $this->assertInstanceOf(MongoDBAdapter::class, $adapter);
    }

    public function testGetItemsErrorInvalidClassToUnserialize()
    {
        $managerMock = new class {
            public function executeQuery($namespace, Query $query, $options = []) {
                return new class {};
            }
        };
        $collection = $this->prophesize(Collection::class);
        $collection->getManager()->willReturn($managerMock);
        $collection->getNamespace()->willReturn(__NAMESPACE__);

        $adapter = new MongoDBAdapter($collection->reveal(), [], [], \stdClass::class);
        $this->expectException(InvalidClass::class);
        $adapter->getItems(0, 25);
    }

    public function testGetItemsSuccessWithEntityClass()
    {
        $managerMock = new class {
            public function executeQuery($namespace, Query $query, $options = []) {
                // mocks cursor object
                return new class {
                    private $typeMap;
                    public function setTypeMap(array $map) : void
                    {
                        $this->typeMap = $map;
                    }
                    public function toArray() : array
                    {
                        $item1 = (new $this->typeMap['root']);
                        $item1->bsonUnserialize(['_id' => 1, 'foo' => 11, 'bar' => 111]);
                        $item2 = (new $this->typeMap['root']);
                        $item2->bsonUnserialize(['_id' => 2, 'foo' => 22, 'bar' => 222]);
                        return [$item1, $item2];
                    }
                };
            }
        };
        $collection = $this->prophesize(Collection::class);
        $collection->getManager()->willReturn($managerMock);
        $collection->getNamespace()->willReturn(__NAMESPACE__);

        $adapter = new MongoDBAdapter($collection->reveal(), [], [], EntityClass::class);
        $items = $adapter->getItems(0, 25);
        $this->assertNotEmpty($items);
        $this->assertIsArray($items);
        $this->assertInstanceOf(EntityClass::class, $items[0]);
    }

    public function testGetItemsSuccessWithoutEntityClass()
    {
        $managerMock = new class {
            public function executeQuery($namespace, Query $query, $options = []) {
                // mocks cursor object
                return new class {
                    public function toArray() : array
                    {
                        return [new BSONDocument(), new BSONDocument()];
                    }
                };
            }
        };
        $collection = $this->prophesize(Collection::class);
        $collection->getManager()->willReturn($managerMock);
        $collection->getNamespace()->willReturn(__NAMESPACE__);

        $adapter = new MongoDBAdapter($collection->reveal());
        $items = $adapter->getItems(0, 25);
        $this->assertNotEmpty($items);
        $this->assertIsArray($items);
        $this->assertNotInstanceOf(EntityClass::class, $items[0]);
    }

    public function testCount()
    {
        $collection = $this->prophesize(Collection::class);
        $collection->countDocuments(Argument::any(), Argument::any())->willReturn(30);

        $adapter = new MongoDBAdapter($collection->reveal());
        $this->assertEquals(30, $adapter->count());
    }
}
