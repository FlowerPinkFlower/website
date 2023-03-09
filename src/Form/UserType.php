<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
        
        ->add('email')
        // ->add('firstname')
        // ->add('lastname')
        ->add('password')
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
