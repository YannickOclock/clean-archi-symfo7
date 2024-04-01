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
        $menuCategories = MenuCategory::createFromCategories(
            [
                ['name' => 'category1', 'subCategories' => ['subCategory1', 'subCategory2']],
                ['name' => 'category2']
            ]
        );
        $this->assertEquals('category1', $menuCategories[0]->getName());
        $this->assertEquals('category2', $menuCategories[1]->getName());
    }

    public function testGetSubCategories()
    {
        $menuCategory = new MenuCategory('category1', [new MenuCategory('subCategory1'), new MenuCategory('subCategory2')]);
        $this->assertEquals('subCategory1', $menuCategory->getSubCategories()[0]->getName());
        $this->assertEquals('subCategory2', $menuCategory->getSubCategories()[1]->getName());
    }

    public function testToString()
    {
        $menuCategory = new MenuCategory('category1');
        $this->assertEquals('category1', (string) $menuCategory);
    }
}