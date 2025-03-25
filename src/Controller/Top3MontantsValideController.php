<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Top3MontantsValideController extends AbstractController
{
    #[Route('/top3', name: 'app_top3')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $moisList = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Aout',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];

        $selectedMonth = $request->request->get('mois');
        $ficheFrais = [];

        if ($selectedMonth) {
            try {
                // Construire une date pour le 1er du mois de 2024
                $selectedDate = new \DateTime("2024-$selectedMonth-01");
                // Filtrer les fiches pour le mois et l'année
                $ficheFrais = $em->getRepository(FicheFrais::class)->findBy(['mois' => $selectedDate]);

                // Trier par montant validé, décroissant
                usort($ficheFrais, function ($a, $b) {
                    return $b->getMontantValid() <=> $a->getMontantValid();
                });

                // Garder uniquement les 3 premiers
                $ficheFrais = array_slice($ficheFrais, 0, 3);
                //dd($ficheFrais);
            } catch (\Exception $e) {
                // Gérer les erreurs éventuelles de création de date
                $this->addFlash('error', 'Une erreur est survenue avec la date sélectionnée.');
            }
        }


        return $this->render('top3/index.html.twig', [
            'moislist' => $moisList,
            'selectedMonth' => $selectedMonth,
            'ficheFrais' => $ficheFrais,
        ]);
    }
}
