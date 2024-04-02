<?php

namespace App\Infrastructure\Symfony\Form\Admin;

use App\Infrastructure\Symfony\Entity\Category;
use App\Infrastructure\Symfony\Entity\Product;
use App\Infrastructure\Symfony\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Entrez le nom du produit'
            ])
            ->add('description', null, [
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Entrez la description'
            ])
            ->add('price', MoneyType::class, [
                'divisor' => 100,
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Entrez le prix'
            ])
            ->add('stock', null, [
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Entrez le stock disponible'
            ])
            ->add('active', CheckboxType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'label' => 'Est en ligne ?',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'group_by' => 'parent.name',
                'query_builder' => function (CategoryRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL')
                        ->orderBy('c.orderMenu', 'ASC');
                },
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Catégorie',
                //'mapped' => false,
            ])
            ->add('images', FileType::class, [
                'required' => false,
                'multiple' => true,
                'mapped' => false,   // Permettre au champs de ne pas être attaché à l'entité
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Images du produit'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}