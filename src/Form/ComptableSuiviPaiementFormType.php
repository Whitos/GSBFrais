<?php

namespace App\Form;

use App\Entity\FicheFrais;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComptableSuiviPaiementFormType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // IDs des états "Validée" et "Mise en paiement"
        $etatsIds = [4, 5]; // Ajustez selon vos IDs réels

        $builder
            ->add('ficheFrais', EntityType::class, [
                'class' => FicheFrais::class,
                'query_builder' => function (EntityRepository $er) use ($etatsIds) {
                    return $er->createQueryBuilder('f')
                        ->join('f.etat', 'e')
                        ->where('e.id IN (:etats)')
                        ->setParameter('etats', $etatsIds)
                        ->orderBy('f.mois', 'DESC');
                },
                'choice_label' => function (FicheFrais $fiche) {
                    $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Paris', \IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
                    $moisFormatte = $formatter->format($fiche->getMois());

                    return $fiche->getUser()->getNom() . ' ' . $fiche->getUser()->getPrenom() . ' - ' . $moisFormatte;
                },
                'placeholder' => 'Choisir une fiche de frais',
                'required' => true,
                'label' => 'Sélectionnez une fiche à traiter',
            ])
            ->add('rechercher', SubmitType::class, [
                'label' => 'Afficher la fiche',
                'attr' => ['class' => 'search-button flex items-center']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}