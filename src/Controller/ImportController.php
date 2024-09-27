<?php

namespace App\Controller;

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
}
