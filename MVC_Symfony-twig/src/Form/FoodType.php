<?php

namespace App\Form;

use App\Entity\Floor;
use App\Entity\Food;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('imageFood', FileType::class, [
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                /*'constraints' => [
                    new File([
                        'maxSize' => '10M',
//                        'mimeTypes' => [
//                            'application/pdf',
//                            'application/x-pdf',
//                        ],
                        'mimeTypesMessage' => 'Please upload a valid image document',
                    ])
                ],*/
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'choices'  => [
                    'vegetable' => 'vegetable',
                    'meat' => 'meat',
                    'poultry' => 'poultry',
                    'fish' => 'fish',
                    'dairy food' => 'dairy food',
                    'condiments (sauce)' => 'condiment (sauce)',
                    'grain food (bread)' => 'grain (food)',
                    'other' => 'other'
                ],
            ])
            ->add('dateOfPurchase', DateType::class, [
                'required' => true,
            ])
            ->add('expiration_date', DateType::class, [
                'required' => true,
            ])
            ->add('quantity', IntegerType::class, [
                'required' => true,
                'attr' => array('min' => 1)
            ])
            ->add('unitQty', ChoiceType::class, [
                'required' => true,
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
