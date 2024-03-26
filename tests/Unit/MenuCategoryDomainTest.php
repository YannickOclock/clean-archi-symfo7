<?php

namespace App\Tests\Unit;

use App\Domain\Home\Entity\MenuCategory;

class MenuCategoryDomainTest extends \PHPUnit\Framework\TestCase
{
    public function testMenuCategory()
    {
        $menuCategory = new MenuCategory('category1');
        $this->assertEquals('category1', $menuCategory->getName());
    }

    public function testCreateFromCategories()
    {
        $menuCategories = MenuCategory::createFromCategories(['category1', 'category2']);
        $this->assertEquals('category1', $menuCategories[0]->getName());
        $this->assertEquals('category2', $menuCategories[1]->getName());
    }
    public function testToString()
    {
        $menuCategory = new MenuCategory('category1');
        $this->assertEquals('category1', (string) $menuCategory);
    }
}