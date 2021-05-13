<?php

namespace App\Form;


use App\Entity\Category;
use App\Entity\Product;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;

/**
 * @method getImages()
 */
class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('info', TextType::class,[
                'required'=>false
            ])
            ->add('price')
            ->add('discountPrice')
            ->add(
                'categories',
                // select sur une entité Doctrine
                EntityType::class,
                [
                    'label' => 'Categories',
                    // nom de l'entité (nom
                    'class' => Category::class,
                    // nom de l'attribut de catégory qui s'affiche dans le select
                    //                    'choice_label'=>'category',
                    'placeholder' => 'chose a Category'
                ]
            )

            ->add('mainImageFile', FileType::class, [
                'label'=> 'mainImage (Image File)',
                'required' => false,
                'constraints' => [
                    new ConstraintsFile([
                        'mimeTypes' => [
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Please upload a jpeg image document',
                    ])

                ]
            ])

            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageEmbeddableType::class,
                    'required' => true,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]
            )



            ->add(
                'stocks',
                CollectionType::class,
                [
                    'entry_type' => StockEmbeddableType::class,
                    'required' => false,
                    'allow_delete' => true,
                    'allow_add' => true,
                    'by_reference' => false,
                ]
            );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
