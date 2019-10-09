<?php

namespace App\Form;

use App\Entity\Fridge;
use App\Entity\Floor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FridgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required'   => true,
                ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'choices'  => [
                    'french door fridge' => 'french door fridge',
                    'side by side fridge' => 'side by side fridge',
                    'freezerless fridge' => 'freezerless fridge',
                    'bottom freezer fridge' => 'bottom freezer fridge',
                    'top freezer fridge' => 'top freezer fridge',
                    'freezer' => 'freezer',
                    'wine fridge' => 'wine fridge',
                ],
            ])
            ->add('nbr_floors')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fridge::class,
        ]);
    }
}
