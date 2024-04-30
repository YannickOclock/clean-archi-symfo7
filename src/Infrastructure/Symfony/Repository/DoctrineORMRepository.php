<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Iterator;

/**
 * Class DoctrineORMRepository
 * 
 * This is a custom abstract Doctrine ORM repository. It is meant to be extended by
 * every Doctrine ORM repository existing in your project.
 * 
 * The main features and differences with the EntityRepository provided by Doctrine is
 * that this one implements our repository contract in an immutable way.
 * 
 */
abstract class DoctrineORMRepository implements Repository
{
    /**
     * This is Doctrine's Entity Manager. It's fine to expose it to the child class.
     * 
     * @var EntityManagerInterface
     */
    protected $manager;
    /**
     * We don't want to expose the query builder to child classes.
     * This is so we are sure the original reference is not modified.
     * 
     * We control the query builder state by providing clones with the `query`
     * method and by cloning it with the `filter` method.
     *
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * DoctrineORMRepository constructor.
     * @param EntityManagerInterface $manager
     * @param string $entityClass
     * @param string $alias
     */
    public function __construct(EntityManagerInterface $manager, string $entityClass, string $alias)
    {
        $this->manager = $manager;
        $this->queryBuilder = $this->manager->createQueryBuilder()
            ->select($alias)
            ->from($entityClass, $alias);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Iterator
    {
        yield from new Paginator($this->queryBuilder->getQuery());
    }

    /**
     * @inheritDoc
     */
    public function slice(int $start, int $size = 20): Repository
    {
        return $this->filter(static function (QueryBuilder $qb) use ($start, $size) {
            $qb->setFirstResult($start)->setMaxResults($size);
        });
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $paginator = new Paginator($this->queryBuilder->getQuery());
        return $paginator->count();
    }

    /**
     * Filters the repository using the query builder
     *
     * It clones it and returns a new instance with the modified
     * query builder, so the original reference is preserved.
     *
     * @param callable $filter
     * @return $this
     */
    protected function filter(callable $filter): self
    {
        $cloned = clone $this;
        $filter($cloned->queryBuilder);
        return $cloned;
    }

    /**
     * Returns a cloned instance of the query builder
     *
     * Use this to perform single result queries.
     *
     * @return QueryBuilder
     */
    protected function query(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }

    /**
     * We allow cloning only from this scope.
     * Also, we clone the query builder always.
     */
    protected function __clone()
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }
}