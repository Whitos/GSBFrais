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
        $choices = [];
        foreach ($options['ficheFraisCollection'] as $ficheFrais) {
            $moisLabel = $ficheFrais->getMois()->format('M Y');
            $choices[$moisLabel] = $ficheFrais->getId();
        }

        $builder
            ->add('mois', ChoiceType::class, [
                'choices' => $choices,
                'required' => true,
                'label' => 'Sélectionnez un mois',
                'placeholder' => 'Choisir un mois',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'ficheFraisCollection' => [], // Expect an array of FicheFrais objects
        ]);
    }
}
