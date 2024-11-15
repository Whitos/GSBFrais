<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\FicheFraisType;
use App\Form\MoisFicheSelectorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ficheFraisCollection = $entityManager->getRepository(FicheFrais::class)->findBy([
            'user' => $this->getUser(),
        ]);
        $form = $this->createForm(MoisFicheSelectorType::class, null, [
            'ficheFraisCollection' => $ficheFraisCollection,
        ]);
        $form->handleRequest($request);
        $ficheFrais = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $ficheFrais = $form->getData();

            return $this->redirectToRoute('app_home', [$ficheFrais], Response::HTTP_SEE_OTHER);

        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form,
            'ficheFrais' => $ficheFrais,

        ]);
    }
}