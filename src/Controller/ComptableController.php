<?php

namespace App\Controller;

use App\Repository\FicheFraisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable')]
    public function index(): Response
    {
        return $this->render('comptable/index.html.twig', [
            'controller_name' => 'ComptableController',
        ]);
    }

    #[Route('/comptable/top3Visiteurs', name: 'app_comptable_top3_visiteurs')]
    public function top3Visiteurs(FicheFraisRepository $repository, Request $request): Response
    {
        $mois = $request->query->get('mois', date('Ym'));
        $top3 = $repository->findTop3VisiteursByMois($mois);

        return $this->render('comptable/index.html.twig', [
            'mois' => $mois,
            'top3' => $top3,
        ]);
    }
}
