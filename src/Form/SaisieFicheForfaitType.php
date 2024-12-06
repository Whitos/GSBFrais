<?php

namespace App\Form;

use App\Entity\FicheFrais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisieFicheForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('forfaitKm', IntegerType::class, [
                'label' => 'Frais Kilométriques',
                'required' => false,
                'attr' => ['placeholder' => 'Nombre de kilomètres']
            ])
            ->add('forfaitEtape', IntegerType::class, [
                'label' => 'Frais d\'étape',
                'required' => false,
                'attr' => ['placeholder' => 'Nombre d\'étapes']
            ])
            ->add('forfaitNuitee', IntegerType::class, [
                'label' => 'Frais de nuitée',
                'required' => false,
                'attr' => ['placeholder' => 'Nombre de nuitées']
            ])
            ->add('forfaitRepas', IntegerType::class, [
                'label' => 'Frais de repas',
                'required' => false,
                'attr' => ['placeholder' => 'Nombre de repas']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
