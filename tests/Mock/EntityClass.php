<?php
declare(strict_types=1);

namespace MariojrrcTest\Laminas\Paginator\Mock;


use MongoDB\BSON\Unserializable;

class EntityClass implements Unserializable
{
    private $id;
    private $foo;
    private $bar;

    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->foo = $data['foo'];
        $this->bar = $data['bar'];
    }
}
