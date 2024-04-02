<?php

namespace App\Infrastructure\Symfony\Repository;

use App\Infrastructure\Symfony\Entity\Product;
use App\Infrastructure\Symfony\Repository\Trait\PaginationTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    use PaginationTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // -- utilisé côté BACK
    public function findProductsPaginated(int $page, int $limit): array
    {
        $limit = abs($limit);
        $query = $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit - $limit)
        ;
        return $this->paginate($query, $page, $limit);
    }
}