<?php

namespace App\Infrastructure\Symfony\Controller\Admin\Trait;

use App\Infrastructure\Symfony\Entity\Category;

trait BreadcrumbTrait
{
    private function getCategoryLevel(Category $category, int $level = 0): int {
        if($category->getParent() !== null) {
            $level++;
            return $this->getCategoryLevel($category->getParent(), $level);
        } else {
            return $level;
        }
    }

    private function getBreadCrumbs(Category $category, $tab = []): array {
        if($category->getParent() !== null) {
            $tab[$this->getCategoryLevel($category)] = [
                'id' => $category->getParent()->getId(),
                'slug' => $category->getParent()->getSlug(),
                'name' => $category->getParent()->getName()
            ];
            return $this->getBreadCrumbs($category->getParent(), $tab);
        } else {
            ksort($tab);
            return $tab;
        }
    }
}