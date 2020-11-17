<?php

namespace App\Form;

use App\Entity\Address;
use App\Service\Locale\LocaleService;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName', TextType::class, [
            'required'=>false,
            'attr'=>[
                'placeholder'=>'If not yourself'
            ]
        ])
        ->add('lastName', TextType::class, [
            'required'=>false,
            'attr'=>[
                'placeholder'=>'If not yourself'
            ]
        ])
            ->add('country', EntityType::class, [
                'class'=>'App\Entity\Country',
                'placeholder'=>'Choose in the list'
            ])
            ->add('street', TextType::class, [
                'attr'=>[
                    'placeholder'=>'Address'
                ]
            ])
            ->add('zipCode', TextType::class, [
                'required'=>false,
                'attr' => [
                    'placeholder' => 'optional'
                ]

            ]);

            

        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $country = $form->getData();

                $countryCode = $country->getCode();
                $this->addCityAndPhoneField($form->getParent(), $countryCode);
              
            }
        );

       
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                $country = $data->getCountry();
                $form = $event->getForm();
                if ($country) {
                    $this->addCityAndPhoneField($form, $country->getCode());                   
                } else {
                    $this->addCityAndPhoneField($form, null);                    
                }
            }
        );

                   
    }

    /**
     * Add a city and phone field to the form
     *
     * @param FormInterface $form
     * @param Country $country
     * @return void
     */
    private function addCityAndPhoneField(FormInterface $form, ?string $countryCode)
    {
        $form
           
            ->add('cityJsonId', ChoiceType::class, [
                'label'=>'City',
                'required'=>true, 
                'placeholder'=>'Choose the closest city in the list',
                'choices' => $countryCode ? LocaleService::getCityChoices($countryCode) : [],
            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'required'=>true,
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_choices' => $countryCode ? [$countryCode] : [],
                // 'preferred_country_choices' => ['NG', 'UK', 'US', 'FR'],
            ]);
    }

        

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
