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
                    $options['ficheFraisCollection']
                ],

                'choice_label' => function ($choice, $key): string {
                    return $choice->getMois()->format('M Y');
                },
                'required' => true,

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'ficheFraisCollection' => [],
        ]);
    }
}
