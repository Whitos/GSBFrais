<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\FicheFraisType;
use App\Form\MoisFicheSelectorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(\Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $entityManager): Response
    {
        $ficheFrais = new FicheFrais();
        $form = $this->createForm(MoisFicheSelectorType::class, $ficheFrais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mois = $form->get('mois')->getData();
            $moisDateTime = \DateTime::createFromFormat('m', $mois);
            $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy(['mois' => $moisDateTime]);
            if ($ficheFrais) {
                $form = $this->createForm(MoisFicheSelectorType::class, $ficheFrais);
            } else {
                $ficheFrais = new FicheFrais();
                $ficheFrais->setMois($moisDateTime);
                $form = $this->createForm(FicheFraisType::class, $ficheFrais);
            }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
        ]);
    }
}