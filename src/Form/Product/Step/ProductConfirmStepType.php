<?php

namespace App\Form\Product\Step;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductConfirmStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('confirmHighPrice', CheckboxType::class, [
            'label' => 'Je confirme que ce produit a un prix élevé.',
            'required' => true,
        ]);
    }
}