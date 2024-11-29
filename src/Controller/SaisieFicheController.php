<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\MoisFicheSelectorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisieFicheController extends AbstractController
{
    #[Route('/saisie/fiche', name: 'app_saisie_fiche')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $currentMonth = new \DateTime('first day of this month');

        $fichesFrais = $em->getRepository(FicheFrais::class)->findBy(['user' => $user]);

        $form = $this->createForm(MoisFicheSelectorType::class, null, [
            'ficheFraisCollection' => $fichesFrais,
        ]);

        $form->handleRequest($request);

        $ficheFrais = $em->getRepository(FicheFrais::class)->findOneBy([
            'mois' => $currentMonth,
            'user' => $user
        ]);

        // Si aucune fiche de frais n'existe pour le mois courant, en créer une nouvelle
        if (!$ficheFrais) {
            $ficheFrais = new FicheFrais();
            $ficheFrais->setMois($currentMonth);
            $ficheFrais->setUser($user);
            $em->persist($ficheFrais);
            $em->flush();
        }

        // Si le formulaire est soumis et valide, on affiche la fiche de frais sélectionnée
        if ($form->isSubmitted() && $form->isValid()) {
            $ficheFrais = $form->get('mois')->getData(); // La fiche de frais sélectionnée
        }

        // Récupération des lignes de frais pour la fiche sélectionnée
        $ligneFraisForfait = $ficheFrais->getLignesFraisForfait();
        $ligneFraisHorsForfait = $ficheFrais->getLignesFraisHorsForfait();

        return $this->render('saisie_fiche/index.html.twig', [
            'form' => $form->createView(),
            'ficheFrais' => $ficheFrais,
            'ligneFraisForfait' => $ligneFraisForfait,
            'ligneFraisHorsForfait' => $ligneFraisHorsForfait,
        ]);
    }
}
