<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country', ChoiceType::class, [
                'choices' => [
                    'Poland' => 'Polska',
                    'United States' => 'United States',
                    'Germany' => 'Deutschland',
                ]
            ])
            ->add('province', null, [
                'attr' => [
                    'placeholder' => 'Province',
                ]
            ])
            ->add('city', null, [
                'attr' => [
                    'placeholder' => 'City',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
