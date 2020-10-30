<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\PurchaseLine;
use App\Entity\Size;
use App\Entity\Tint;
use App\Repository\StockRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseLineType extends AbstractType
{
    private $stockRepository;

    public function __construct(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                $product = $data->getProduct();
                $form = $event->getForm();
                $size = $data->getSize();
                $tint = $data->getTint();


                $this->addSizeField($form, $product, $size, $tint);
                $this->addTintField($form, $product, $size, $tint);
                $this->addQuantityField($form, $product, $size, $tint);
            }
        );
    }

    /**
     * Rajoute un champs size au formulaire
     * @param FormInterface $form
     * @param Product $product
     * @param Size|null $size
     * @param Tint|null $tint
     */
    private function addSizeField(FormInterface $form, Product $product, ?Size $size, ?Tint $tint)
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'size',
            EntityType::class,
            null,
            [
                'class' => Size::class,
                'label' => false,
                'placeholder' => 'select your size',
                'mapped' => true,
                'required' => true,
                'auto_initialize' => false,
                'choices' => $this->addSizeChoices($product),
                'attr' =>[
                    'autofocus'=>true
                ]
            ]
        );



        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($tint, $product, $size) {
                $form = $event->getForm();

                $size = $form->getData();

                /**
                 * @var Symfony\Component\Form\FormInterface $form
                 */
                $this->addTintField($form->getParent(), $product, $size, $tint);
            }
        );

        $form->add($builder->getForm());
    }

    private function addTintField(FormInterface $form, Product $product, ?Size $size, ?Tint $tint)
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'tint',
            EntityType::class,
            null,
            [
                'class' => Tint::class,
                'label' => false,
                'placeholder' => 'Select your color',

                'mapped' => true,
                'required' => false,
                'auto_initialize' => false,
                'choices' => $size ? $this->addTintChoices($product, $size) : $this->addTintChoices($product, null)
            ]
        );


        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($size, $product, $tint) {
                $form = $event->getForm();
                $tint = $form->getData();

                /**
                 * @var Symfony\Component\Form\FormInterface $form
                 */
                $this->addQuantityField($form->getParent(), $product, $size, $tint);
            }
        );

        $form->add($builder->getForm());
    }

    private function addQuantityField(FormInterface $form, Product $product, ?Size $size, ?Tint $tint)
    {
        $form->add('quantity', ChoiceType::class, [

            'placeholder' => 'Quantity',
            'label' => false,
            'data' => 1,
            'required' => false,
            'choices' => $tint ? $this->addQuantityChoices($product, $size, $tint) : []

        ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PurchaseLine::class,

        ]);
    }

    public function addSizeChoices(Product $product)
    {
        $sizes = [];
        foreach($product->getSizes() as $size){
            if($stocks = $this->stockRepository->findBy([
                'product'=>$product,
                'size'=>$size
            ])){
                foreach($stocks as $stock){

                    if($stock->getQuantity()>0){
                        $sizes[] = $size;
                    }
                }
            }
        }
        return $sizes;
    }


    private function addTintChoices(Product $product, ?Size $size)
    {
        if ($size) {
            $tints = [];
            foreach($product->getTintsBySize($size) as $tint){
                if($stock = $this->stockRepository->findOneBy([
                    'product'=>$product,
                    'size'=>$size,
                    'tint'=>$tint
                ])){
                    if($stock->getQuantity() >0){
                        $tints[] = $tint;
                    }
                }
            }

            return $tints;
        }

        return [];
    }

    private function addQuantityChoices(Product $product, ?Size $size, ?Tint $tint)
    {
        $choices = [];
        if ($size && $tint) {
           
            $stock = $this->stockRepository->findOneBy([
                'product'=>$product,
                'size'=>$size,
                'tint'=>$tint
            ]);
            
            

            for ($i = 1; $i <= $stock->getQuantity() && $i <= 16; $i++) {
                $choices[] = $i;
            }
            $output = [];
            foreach ($choices as $k => $v) {
                $output[$v] = $k + 1;
            }
            return $output;
            
            
        } else
            return [];
    }
}
