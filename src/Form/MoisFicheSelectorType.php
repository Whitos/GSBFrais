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
        $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Paris', \IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
        $translations = [
            'january' => 'janvier', 'february' => 'février', 'march' => 'mars', 'april' => 'avril',
            'may' => 'mai', 'june' => 'juin', 'july' => 'juillet', 'august' => 'août',
            'september' => 'septembre', 'october' => 'octobre', 'november' => 'novembre', 'december' => 'décembre'
        ];

        foreach ($options['ficheFraisCollection'] as $ficheFrais) {
            $moisLabel = ucfirst($formatter->format($ficheFrais->getMois()->getTimestamp()));
            $moisLabel = ucfirst(strtr($moisLabel, $translations));
            $choices[$moisLabel] = $ficheFrais->getId();
        }

        $builder->add('mois', ChoiceType::class, [
            'choices' => $choices,
            'required' => true,
            'label' => 'Sélectionnez un mois',
            'placeholder' => 'Choisir un mois',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'ficheFraisCollection' => [],
        ]);
    }
}