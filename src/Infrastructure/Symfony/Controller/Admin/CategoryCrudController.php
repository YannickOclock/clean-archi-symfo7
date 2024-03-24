<?php

namespace App\Infrastructure\Symfony\Controller\Admin;

use App\Infrastructure\Symfony\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            //IdField::new('id'),
            TextField::new('name'),
            BooleanField::new('active'),
            // Liste des sous catégories
            AssociationField::new('parent'),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $category = new Category();
        $category->setCreatedAt(new \DateTimeImmutable());
        return $category;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::updateEntity($entityManager, $entityInstance);
    }
}
