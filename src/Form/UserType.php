<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('firstName')
            ->add('lastName')
            ->add('adresse')
            ->add('gender')
            ->add('isClient', CheckboxType::class, [
                'label' => 'Créer en tant que client ?',
                'required' => false,
                'mapped' => false,
            ])
            ->add('numClient', TextType::class, [
                'label' => 'Numéro de client (laissé vide pour autogénération)',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'ex: 1234567890'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d{10}$/',
                        'message' => 'Le numéro du client doit contenir exactement 10 chiffres'
                    ])
                ]
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
