<?php

namespace App\Infrastructure\Symfony\Form\Admin;

use App\Infrastructure\Symfony\Entity\Category;
use App\Infrastructure\Symfony\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Entrez le nom de la catégorie'
            ])
            ->add('description', null, [
                'attr' => [
                    'class' => 'form-control my-2',
                    'rows' => '20'
                ],
                'label' => 'Entrez la description'
            ])
            ->add('parent', EntityType::class, [
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'group_by' => 'parent.name',
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Catégorie parente'
            ])
            ->add('active', CheckboxType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'label' => 'Est en ligne ?',
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'multiple' => false,
                'mapped' => false,   // Permettre au champs de ne pas être attaché à l'entité
                'attr' => [
                    'class' => 'form-control my-2'
                ],
                'label' => 'Image de la catégorie'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}