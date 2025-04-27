<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\FicheFrais;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComptableValidationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $visiteur = $options['visiteur'] ?? null;

        $builder
            ->add('visiteur', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_VISITEUR%')
                        ->orderBy('u.nom', 'ASC');
                },
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'placeholder' => 'Choisir un visiteur',
                'required' => true,
                'label' => 'Sélectionnez un visiteur',
                'data' => $visiteur,
            ]);

        // Ajouter le champ 'mois' seulement si un visiteur est sélectionné
        if ($visiteur) {
            $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Paris', \IntlDateFormatter::GREGORIAN, 'MMMM yyyy');
            $translations = [
                'january' => 'janvier', 'february' => 'février', 'march' => 'mars', 'april' => 'avril',
                'may' => 'mai', 'june' => 'juin', 'july' => 'juillet', 'august' => 'août',
                'september' => 'septembre', 'october' => 'octobre', 'november' => 'novembre', 'december' => 'décembre'
            ];

            $builder->add('mois', EntityType::class, [
                'class' => FicheFrais::class,
                'query_builder' => function (EntityRepository $er) use ($visiteur) {
                    return $er->createQueryBuilder('f')
                        ->where('f.user = :user')
                        ->setParameter('user', $visiteur)
                        ->orderBy('f.mois', 'DESC');
                },
                'choice_label' => function (FicheFrais $fiche) use ($formatter, $translations) {
                    $moisLabel = ucfirst($formatter->format($fiche->getMois()));
                    return ucfirst(strtr(strtolower($moisLabel), $translations));
                },
                'placeholder' => 'Choisir un mois',
                'required' => true,
                'label' => 'Sélectionnez un mois',
            ]);

        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);

        $resolver->setDefined(['visiteur']);
        $resolver->setAllowedTypes('visiteur', ['null', User::class]);
    }
}