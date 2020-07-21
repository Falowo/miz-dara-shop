<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\ParentCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'parent',
                // select sur une entité Doctrine
                EntityType::class,
                [
                    'label'=>'parent',
                    // nom de l'entité (nom
                    'class' => Category::class,
                    // nom de l'attribut de catégory qui s'affiche dans le select
//                    'choice_label'=>'name',
                    'placeholder'=> 'chose a parentCategory'
                ]
            )
            ->add(
                'categories',
                CollectionType::class,
                [
                    'entry_type' => CategoryEmbeddableType::class,
                    'required' => false,
                    'allow_delete' => true,
                    'allow_add' => true,
                    'by_reference' => false,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
