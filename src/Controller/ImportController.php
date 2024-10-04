<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
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

    #[Route('/import/user', name: 'app_import_user')]
    public function lireVisiteurJson(EntityManagerInterface $entityManager): JsonResponse
    {
        $chemin = $this->getParameter('kernel.project_dir') . '/public/visiteur.json';

        if (!file_exists($chemin)) {
            return new JsonResponse(['error' => 'Fichier non trouvé'], 404);
        }

        $contenuJson = file_get_contents($chemin);
        $visiteurData = json_decode($contenuJson);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Fichier JSON invalide' . json_last_error_msg()], 500);
        }

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

    #[Route('/import/fichefrais', name: 'app_import_fichefrais')]
    public function lireFicheFraisJson(EntityManagerInterface $entityManager): JsonResponse
    {
        $chemin = $this->getParameter('kernel.project_dir') . '/public/fichefrais.json';

        if (!file_exists($chemin)) {
            return new JsonResponse(['error' => 'Fichier non trouvé'], 404);
        }

        $contenuJson = file_get_contents($chemin);
        $ficheFraisData = json_decode($contenuJson);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Fichier JSON invalide' . json_last_error_msg()], 500);
        }


        $etatMapping = [
            'CL' => 1,
            'CR' => 2,
            'RB' => 3,
            'VA' => 4,
        ];
        foreach ($ficheFraisData as $ficheFrais) {
            $ficheFraisVisiteur = new FicheFrais();

            $mois = \DateTime::createFromFormat('Ym', substr($ficheFrais->mois, 0, 6));
            $mois->setDate($mois->format('Y'), $mois->format('m'), 1);
            $ficheFraisVisiteur->setMois($mois);
            $ficheFraisVisiteur->setNbJustificatifs($ficheFrais->nbJustificatifs);
            $ficheFraisVisiteur->setMontantValid($ficheFrais->montantValide);
            $ficheFraisVisiteur->setDateModif(new \DateTime($ficheFrais->dateModif));

            $etatId = $etatMapping[$ficheFrais->idEtat] ?? null;
            $etat = $entityManager->getRepository(Etat::class)->find($etatId);
            $ficheFraisVisiteur->setEtat($etat);

            $user = $entityManager->getRepository(User::class)->findOneBy(['oldId' => $ficheFrais->idVisiteur]);
            $ficheFraisVisiteur->setUser($user);

            $entityManager->persist($ficheFraisVisiteur);
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

        foreach ($ligneFraisForfaitData as $ligneFraisForfait) {
            $ligneFraisForfaitVisiteur = new LigneFraisForfait();

            $ligneFraisForfaitVisiteur->setQuantite($ligneFraisForfait->quantite);

            $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy(['mois' => $ligneFraisForfait->mois]);
            $ligneFraisForfaitVisiteur->setFicheFrais($ficheFrais);

            $fraisForfait = $entityManager->getRepository(FraisForfait::class)->findOneBy(['id' => $ligneFraisForfait->idFraisForfait]);
            $ligneFraisForfaitVisiteur->setFraisForfait($fraisForfait);

            $entityManager->persist($ligneFraisForfaitVisiteur);
            $entityManager->flush();
        }

        return new JsonResponse(['success' => 'Lignes de frais forfait importées avec succès'], 200);
    }
}
