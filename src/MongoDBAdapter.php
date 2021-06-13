<?php

declare(strict_types=1);

namespace Mariojrrc\Laminas\Paginator;

use Laminas\Paginator\Adapter\AdapterInterface;
use Mariojrrc\Laminas\Paginator\Exception\InvalidClass;
use MongoDB\BSON\Unserializable;
use MongoDB\Collection;
use MongoDB\Driver\Query;

use function class_exists;
use function class_implements;
use function in_array;
use function sprintf;

class MongoDBAdapter implements AdapterInterface
{
    private Collection $collection;
    private array $conditions;
    private array $options;
    /** @var string|null if informed it must implements MongoDB\BSON\Unserializable interface */
    private ?string $entityClass = null;

    public function __construct(
        Collection $collection,
        array $conditions = [],
        array $options = [],
        ?string $entityClass = null
    ) {
        $this->collection  = $collection;
        $this->conditions  = $conditions;
        $this->options     = $options;
        $this->entityClass = $entityClass;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Laminas\Paginator\Adapter\AdapterInterface::getItems()
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->options['skip']  = $offset;
        $this->options['limit'] = $itemCountPerPage;

        $query  = new Query($this->conditions, $this->options);
        $cursor = $this->collection->getManager()->executeQuery($this->collection->getNamespace(), $query);

        if ($this->entityClass !== null && class_exists($this->entityClass)) {
            if (! in_array(Unserializable::class, class_implements($this->entityClass), true)) {
                throw new InvalidClass(sprintf(
                    'Class %s must implement %s interface',
                    $this->entityClass,
                    Unserializable::class
                ));
            }

            $cursor->setTypeMap(['root' => $this->entityClass]);
        }

        return $cursor->toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @see Countable::count()
     */
    public function count()
    {
        return $this->collection->countDocuments($this->conditions, $this->options);
    }
}
