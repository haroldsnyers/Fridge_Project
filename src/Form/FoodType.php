<?php

namespace App\Form;

use App\Entity\Floor;
use App\Entity\Food;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('imageFoodPath')
            ->add('type')
            ->add('dateOfPurchase', DateType::class, [
                'widget' => 'choice',
                'input'  => 'datetime_immutable',
            ])
            ->add('expiration_date', DateTimeType::class)
            ->add('quantity')
            ->add('unitQty', ChoiceType::class, [
                'choices'  => [
                    'kg' => 'kg',
                    'Liter' => 'Liter',
                    'pieces' => 'pieces',
                ],
            ])
            ->add('id_floor', EntityType::class, [
                'class' => Floor::class,
                'choices' => $options['floors'],
                'required' => $options['floors']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Food::class,
            'floors' => null,
        ]);
    }
}
