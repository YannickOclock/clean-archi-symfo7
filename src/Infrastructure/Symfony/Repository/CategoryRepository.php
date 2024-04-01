<?php

namespace App\Infrastructure\Symfony\Repository;

use App\Domain\Home\Entity\MenuCategory;
use App\Domain\Home\Repository\CategoryRepositoryInterface;
use App\Infrastructure\Symfony\Entity\Category;
use App\Infrastructure\Symfony\Repository\Trait\PaginationTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    use PaginationTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    // -- utilisé par la partie ADMIN
    public function findCategoriesPaginated(int $page, int $limit): array
    {
        $limit = abs($limit);
        $query = $this->createQueryBuilder('c')
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit - $limit)
            ->orderBy('c.orderMenu', 'ASC')
            ->where('c.parent is null')
        ;
        $results = $this->paginate($query, $page, $limit);
        return $results;
    }
    public function findSubCategoriesPaginated(Uuid $categoryId, int $page, int $limit): array
    {
        $limit = abs($limit);
        $query = $this->createQueryBuilder('c')
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit - $limit)
            ->orderBy('c.orderMenu', 'ASC')
            ->where('c.parent = :categoryId')
        ;
        $query->setParameter('categoryId', $categoryId, UuidType::NAME);
        $results = $this->paginate($query, $page, $limit);
        return $results;
    }
    public function findPreviousOrNextCategoryInSameLevel(String $position, int $categoryOrder, Uuid $categoryParentId = null): array
    {
        $query = $this->createQueryBuilder('c')
            ->setMaxResults(1)
        ;

        // - position
        if($position === "down") {
            $query
                ->where('c.orderMenu > '.$categoryOrder)
                ->orderBy('c.orderMenu', 'ASC');
        } elseif($position === "up") {
            $query
                ->where('c.orderMenu < '.$categoryOrder)
                ->orderBy('c.orderMenu', 'DESC');
        }

        // - parent ou non ?
        if($categoryParentId === null) {
            $query->andWhere('c.parent is null');
        } else {
            $query->andWhere('c.parent = :categoryParentId');
            $query->setParameter('categoryParentId', $categoryParentId, UuidType::NAME);
        }

        $results = $query->getQuery()->getResult();
        return $results;
    }



    // -- utilisé par la partie FRONT / DOMAIN

    /**
     * @return MenuCategory[]
     */
    public function findAllMenuCategories(): array
    {
        $categories = $this->findBy(['parent' => null], ['orderMenu' => 'ASC']);
        $categories = array_map(function(Category $category) {
            $subCategories = $category->getSubCategories()->map(function(Category $category) {
                return $category->getName();
            })->toArray();
            return [
                'name' => $category->getName(),
                'subCategories' => $subCategories
            ];
        }, $categories);
        dump($categories);
        return MenuCategory::createFromCategories($categories);
    }
}
