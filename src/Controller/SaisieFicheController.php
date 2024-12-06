<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\MoisFicheSelectorType;
use App\Form\SaisieFicheForfaitType;
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

        // Obtenir le mois courant
        $moisActuel = new \DateTime('first day of this month');

        // Chercher la fiche de frais du mois courant
        $ficheFrais = $em->getRepository(FicheFrais::class)
            ->findOneBy([
                'user' => $user,
                'mois' => $moisActuel
            ]);

        if (!$ficheFrais) {
            $uneFicheFrais = new FicheFrais();
            $uneFicheFrais->setUser($this->getUser());
            $uneFicheFrais->setMois($moisActuel);
            $uneFicheFrais->setDateModif(new \DateTime());
            $uneFicheFrais->setNbJustificatifs(0);
            $uneFicheFrais->setMontantValid(0);
            // Créer les lignes de frais forfaitisés
        }

        $formFraisForfaits = $this->createForm(SaisieFicheForfaitType::class, $ficheFrais);
        $formFraisForfaits->handleRequest($request);

        if ($formFraisForfaits->isSubmitted() && $formFraisForfaits->isValid()) {
            //mise à jour de la date de modification
            $ficheFrais->setDateModif(new \DateTime());
            //mise à jour des quantités de frais forfaitisés
            $km = $formFraisForfaits->get('forfaitKm')->getData();
            $ficheFrais->getLignesFraisForfait()->get(0)->setQuantite($km);

            $em->persist($ficheFrais);
            $em->flush();
        }

        return $this->render('saisie_fiche/index.html.twig', [
            'formForfaits' => $formFraisForfaits->createView(),
        ]);




    }
}
