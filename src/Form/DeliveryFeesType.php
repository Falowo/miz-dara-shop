<?php

namespace App\Form;

use App\Entity\DeliveryFees;
use App\Service\Locale\LocaleService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryFeesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('title')
            ->add('maxDays')
            ->add('transportService')
            ->add('ngCity')
            ->add('ngState')
            ->add('country')
            ->add('continent')
            ->add('fixedAmount', MoneyType::class, [
                'required'=>false,
                'currency'=>'NGN'
          
            ])
            ->add('variableAmount', PercentType::class, [
                'required'=>false,
            ])
            ->add('freeForMoreThan', MoneyType::class, [
                'required'=>false,
                'currency'=>'NGN'
                
            ])

        ;

       
        
    }

    


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DeliveryFees::class,
        ]);
    }
}
