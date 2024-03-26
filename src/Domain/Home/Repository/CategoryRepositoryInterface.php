<?php

namespace App\Domain\Home\Repository;

use App\Domain\Home\Entity\MenuCategory;

interface CategoryRepositoryInterface
{
    /**
     * @return MenuCategory[]
     */
    public function findAllMenuCategories(): array;
}