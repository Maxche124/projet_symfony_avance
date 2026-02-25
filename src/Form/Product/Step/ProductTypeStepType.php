<?php

namespace App\Form\Product\Step;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductTypeStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, [
            'label' => 'Type de produit',
            'choices' => [
                'Produit physique' => 'physical',
                'Produit numérique' => 'digital',
            ],
            'expanded' => true,
        ]);
    }
}