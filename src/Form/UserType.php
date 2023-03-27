<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('roles', ChoiceType::class, ['choices' =>[
            'utilisateur'=>"ROLE_USER",
            'adminstrateur'=>"ROLE_ADMIN"
        ],
        'expanded'=>true,
        'multiple'=>true,
        'label'=>"Roles"
        ])
        // ->add('id')
        ->add('email', null, [
            'label' => 'Email'
            ])
            ->add('firstname', null, [
                'label' => 'Prénom'
        ])
        ->add('lastname', null, [
            'label' => 'Nom'
            ])
            // ->add('birthdate')
            
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
            'format' => 'dd-MM-yyyy',
            'html5' => false,
            'attr' => [
                'class' => 'datepicker',
                'autocomplete' => 'off',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez renseigner votre date de naissance',
                ]),
            ],
        ])
              
        

        ->add('password', null, [
                'label' => 'Mot de passe'
            ])
        
            ;
        }
        
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
