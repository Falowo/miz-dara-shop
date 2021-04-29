<?php

namespace App\Form;

use App\Entity\Category;
use Doctrine\DBAL\Types\ObjectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBarType extends AbstractType
{
    private $data;

    public function __construct(?ObjectType $data)
    {
        $this->data = $data;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->data = $builder->getData();

        $builder
            ->add('orderby', ChoiceType::class, [
                'choices' => $this->getOrderChoices(),
                'label' => false,
                'placeholder' => 'Sort by',
                'required' => false

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    private function getOrderChoices()
    {
        $choices = Category::ORDERBY;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $v;
        }

        return $output;
    }

}
