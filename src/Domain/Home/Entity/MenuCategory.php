<?php

namespace App\Domain\Home\Entity;

class MenuCategory {
    private string $name;
    /** @var MenuCategory[] $subCategories */
    private array $subCategories = [];

    public function __construct(string $name, array $subCategories = []) {
        $this->name = $name;
        $this->subCategories = $subCategories;
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @return MenuCategory[]
     */
    public function getSubCategories(): array {
        return $this->subCategories;
    }

    /**
     * @param array $categories [['name' => 'category1', 'subCategories' => ['subCategory1', 'subCategory2']]]
     * @return MenuCategory[]
     */
    public static function createFromCategories(array $categories): array {
        return array_map(function(array $category) {
            return new MenuCategory($category['name'], array_map(function(string $subCategory) {
                return new MenuCategory($subCategory);
            }, $category['subCategories'] ?? []));
        }, $categories);
    }

    public function __toString(): string {
        return $this->name;
    }
}

