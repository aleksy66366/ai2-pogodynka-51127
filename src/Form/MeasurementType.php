<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Measurement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('celsius', NumberType::class, [
                'scale' => 1,
                'attr' => [
                    'min' => -100,
                    'max' => 100,
                    'step' => 0.1,
                ],
                'html5' => true,
            ])
            ->add('rain', NumberType::class, [
                'scale' => 2,
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 0.01,
                ],
                'html5' => true,
            ])
            ->add('cloud', ChoiceType::class, [
                'choices' => [
                    '0' => 'Bezchmurnie',
                    '1' => 'Prawie bezchmurnie',
                    '2' => 'Małe zachmurzenie',
                    '3' => 'Częściowe zachmurzenie',
                    '4' => 'Umiarkowane zachmurzenie',
                    '5' => 'Duże zachmurzenie',
                    '6' => 'Prawie całkowite zachmurzenie',
                    '7' => 'Całkowite zachmurzenie',
                    '8' => 'Zasłonięte całkowicie'
                ]
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Measurement::class,
        ]);
    }
}
