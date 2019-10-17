<?php

namespace App\Form;

use App\Entity\Floor;
use App\Entity\Food;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('expiration_date')
            ->add('quantity')
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
