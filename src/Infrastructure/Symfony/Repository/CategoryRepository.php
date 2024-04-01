<?php

namespace App\Infrastructure\Symfony\Repository;

use App\Domain\Home\Entity\MenuCategory;
use App\Domain\Home\Repository\CategoryRepositoryInterface;
use App\Infrastructure\Symfony\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return MenuCategory[]
     */
    public function findAllMenuCategories(): array
    {
        $categories = $this->findBy(['parent' => null]);
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
