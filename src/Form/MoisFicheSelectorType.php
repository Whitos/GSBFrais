<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoisFicheSelectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mois', ChoiceType::class, [
                'choices' => [
                    'Janvier' => '01',
                    'Février' => '02',
                    'Mars' => '03',
                    'Avril' => '04',
                    'Mai' => '05',
                    'Juin' => '06',
                    'Juillet' => '07',
                    'Août' => '08',
                    'Septembre' => '09',
                    'Octobre' => '10',
                    'Novembre' => '11',
                    'Décembre' => '12',
                ],
                'label' => 'Mois',
                'required' => true,

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
