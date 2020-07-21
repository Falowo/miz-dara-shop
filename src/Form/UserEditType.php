<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\User;
use App\Repository\CountryRepository;
use App\Service\Locale\LocaleService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface as FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TextType;

class UserEditType extends AbstractType
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email')

            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Password',
                        'help' => 'The password must have at least 6 characters, one uppercase and one number'
                    ],
                    'second_options' => [
                        'label' => 'Password\'s confirmation'
                    ],
                    // message si les deux champs n'ont pas la même valeur
                    'invalid_message' => 'The confirmation doesn\'t match the password',
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should have at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),

                        new Regex([
                            'pattern' => "/^(?=.*[0-9])(?=.*[A-Z]).{6,20}$/",
                            'message' => "invalid password"
                        ])

                    ],
                ]
            )
            ->add('firstName')
            ->add('lastName')
            ->add('address')
            ->add('country')
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

                   

        $builder->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
        ]);
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
            ->add('city', ChoiceType::class, [
                'choices' => $countryCode ? LocaleService::getCityChoices($countryCode) : [],

            ])
            ->add('phoneNumber', PhoneNumberType::class, [
                'required'=>false,
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_choices' => $countryCode ? [$countryCode] : [],
                'preferred_country_choices' => ['NG', 'UK', 'US', 'FR'],
            ]);
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
