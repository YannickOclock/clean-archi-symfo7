<?php

namespace App\Domain\Home\Entity;

class MenuCategory {
    private string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @param array $categories
     * @return MenuCategory[]
     */
    public static function createFromCategories(array $categories): array {
        return array_map(fn(string $name) => new self($name), $categories);
    }

    public function __toString(): string {
        return $this->name;
    }
}

