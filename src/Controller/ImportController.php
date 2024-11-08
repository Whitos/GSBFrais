<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImportController extends AbstractController
{
    #[Route('/import', name: 'app_import')]
    public function index(): Response
    {
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
        ]);
    }

    #[Route('/import/user', name: 'app_import_user')]     ////import fait
    public function lireVisiteurJson(EntityManagerInterface $entityManager): JsonResponse
    {
        $chemin = $this->getParameter('kernel.project_dir') . '/public/visiteur.json';

        if (!file_exists($chemin)) {
            return new JsonResponse(['error' => 'Fichier non trouvé'], 404);
        }

        $contenuJson = file_get_contents($chemin);
        $visiteurData = json_decode($contenuJson, false, 512, JSON_THROW_ON_ERROR);

        foreach ($visiteurData as $visiteur) {
            $user = new User();

            $user->setOldId($visiteur->id);
            $user->setNom($visiteur->nom);
            $user->setPrenom($visiteur->prenom);
            $user->setLogin($visiteur->login);
            $user->setPassword($visiteur->mdp);
            $user->setAdresse($visiteur->adresse);
            $user->setCp($visiteur->cp);
            $user->setVille($visiteur->ville);
            $user->setDateEmbauche(new \DateTime($visiteur->dateEmbauche));
            $user->setEmail($visiteur->prenom . '.' . $visiteur->nom . '@example.com');

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return new JsonResponse(['success' => 'Visiteursimportés avec succès'], 200);
    }

    #[Route('/import/fichefrais', name: 'app_import_fichefrais')]      ////import fait
    public function lireFicheFraisJson(EntityManagerInterface $entityManager): JsonResponse
    {
        $chemin = $this->getParameter('kernel.project_dir') . '/public/visiteur.json';

        if (!file_exists($chemin)) {
            return new JsonResponse(['error' => 'Fichier non trouvé'], 404);
        }

        $contenuJson = file_get_contents($chemin);
        $fichesFraisData = json_decode($contenuJson, false, 512, JSON_THROW_ON_ERROR);


        $etatMapping = [
            'CL' => 1,
            'CR' => 2,
            'RB' => 3,
            'VA' => 4,
        ];
        foreach ($fichesFraisData as $ficheFraisData) {
            $ficheFrais = new FicheFrais();

            $mois = \DateTime::createFromFormat('Ym', substr($ficheFraisData->mois, 0, 6));
            $mois->setDate($mois->format('Y'), $mois->format('m'), 1);
            $ficheFrais->setMois($mois);

            $ficheFrais->setNbJustificatifs($ficheFraisData->nbJustificatifs);
            $ficheFrais->setMontantValid($ficheFraisData->montantValide);
            $ficheFrais->setDateModif(new \DateTime($ficheFraisData->dateModif));


            $etatId = $etatMapping[$ficheFraisData->idEtat] ?? null;
            $etat = $entityManager->getRepository(Etat::class)->find($etatId);
            $ficheFrais->setEtat($etat);

            $user = $entityManager->getRepository(User::class)->findOneBy(['oldId' => $ficheFraisData->idVisiteur]);
            $ficheFrais->setUser($user);

            $entityManager->persist($ficheFrais);
            $entityManager->flush();
        }

        return new JsonResponse(['success' => 'Fiches de frais importées avec succès'], 200);
    }

    #[Route('/import/ligneFraisForfait', name: 'app_import_lignefraisforfait')]
    public function lireLigneFraisForfaitJson(EntityManagerInterface $entityManager): JsonResponse
    {
        $chemin = $this->getParameter('kernel.project_dir') . '/public/lignefraisforfait.json';

        if (!file_exists($chemin)) {
            return new JsonResponse(['error' => 'Fichier non trouvé'], 404);
        }

        $contenuJson = file_get_contents($chemin);
        $ligneFraisForfaitData = json_decode($contenuJson);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Fichier JSON invalide' . json_last_error_msg()], 500);
        }

        $fraisForfaitMapping = [
            'ETP' => 1,
            'KM' => 2,
            'NUI' => 3,
            'REP' => 4,
        ];
        foreach ($ligneFraisForfaitData as $ligneFraisForfait) {
            $ligneFraisForfaitVisiteur = new LigneFraisForfait();

            $ligneFraisForfaitVisiteur->setQuantite($ligneFraisForfait->quantite);

            $mois = \DateTime::createFromFormat('Ym', $ligneFraisForfait->mois);
            if ($mois === false) {
                continue;
            }
            $mois->setDate($mois->format('Y'), $mois->format('m'), 1);
            $user = $entityManager->getRepository(User::class)->findOneBy(['oldId' => $ligneFraisForfait->idVisiteur]);
            $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy(['mois' => $mois, 'user' => $user]);
            $ligneFraisForfaitVisiteur->setFichesFrais($ficheFrais);

            $fraisForfaitId = $fraisForfaitMapping[$ligneFraisForfait->idFraisForfait] ?? null;
            $fraisForfait = $entityManager->getRepository(FraisForfait::class)->find($fraisForfaitId);
            $ligneFraisForfaitVisiteur->setFraisForfaits($fraisForfait);

            $entityManager->persist($ligneFraisForfaitVisiteur);
            $entityManager->flush();
        }

        return new JsonResponse(['success' => 'Lignes de frais forfait importées avec succès'], 200);
    }

    #[Route('/import/ligneHorsFraisForfait', name: 'app_import_lignefraishorsforfait')]
    public function lireLigneFraisHorsForfaitJson(EntityManagerInterface $entityManager): JsonResponse
    {

        $chemin = $this->getParameter('kernel.project_dir') . '/public/lignefraishorsforfait.json';

        if (!file_exists($chemin)) {
            return new JsonResponse(['error' => 'Fichier non trouvé'], 404);
        }

        $contenuJson = file_get_contents($chemin);
        $ligneFraisHorsForfaitData = json_decode($contenuJson);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Fichier JSON invalide' . json_last_error_msg()], 500);
        }

        foreach ($ligneFraisHorsForfaitData as $ligneFraisHorsForfait) {

            $ligneFraisHorsForfaitVisiteur = new LigneFraisHorsForfait();

            $ligneFraisHorsForfaitVisiteur->setLibelle($ligneFraisHorsForfait->libelle);
            $ligneFraisHorsForfaitVisiteur->setDate(new \DateTime($ligneFraisHorsForfait->date));
            $ligneFraisHorsForfaitVisiteur->setMontant($ligneFraisHorsForfait->montant);

            $mois = \DateTime::createFromFormat('Ym', $ligneFraisHorsForfait->mois);
            if ($mois === false) {
                continue;
            }
            $mois->setDate($mois->format('Y'), $mois->format('m'), 1);
            $user = $entityManager->getRepository(User::class)->findOneBy(['oldId' => $ligneFraisHorsForfait->idVisiteur]);
            $ligneFraisHorsForfaitVisiteur->setFicheFrais($entityManager->getRepository(FicheFrais::class)->findOneBy([
                'mois' => $mois,
                'user' => $user
            ]));

            $entityManager->persist($ligneFraisHorsForfaitVisiteur);
            $entityManager->flush();
        }

        return new JsonResponse(['success' => 'Lignes de frais hors forfait importées avec succès'], 200);

    }
}
