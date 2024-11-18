<?php

namespace App\Controller;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Form\MoisFicheSelectorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $ficheFraisCollection = $em->getRepository(FicheFrais::class)->findBy(
            ['user' => $this->getUser()]
        );

        $form = $this->createForm(MoisFicheSelectorType::class, null, [
            'ficheFraisCollection' => $ficheFraisCollection,
        ]);

        $form->handleRequest($request);
        $selectedFicheFrais = null;
        $ligneFraisForfait = null;
        $ligneFraisHorsForfait = null;


        if ($form->isSubmitted() && $form->isValid()) {
            $ficheFrais = $form->get('mois')->getData();
            $selectedFicheFrais = $em->getRepository(FicheFrais::class)->find($ficheFrais);
            $ligneFraisForfait = $selectedFicheFrais->getLignesFraisForfait();
            $ligneFraisHorsForfait = $selectedFicheFrais->getLignesFraisHorsForfait();
        }


        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'selectedFicheFrais' => $selectedFicheFrais,
            'lignesFraisForfait' => $ligneFraisForfait,
            'lignesFraisHorsForfait' => $ligneFraisHorsForfait,
        ]);

    }
}