<?php

namespace App\Form;

use App\Entity\Floor;
use App\Entity\Food;
use App\Entity\Fridge;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
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
//                'choice_label' => function(Floor $floor, Request $request) {
//
//                    return sprintf('(%d) %s', $floor->getId(), $floor->getName());
//                },
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
            'floorNames' => null
        ]);
    }
}
