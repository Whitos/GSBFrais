<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Form\MoisFicheSelectorType;
use App\Form\SaisieFicheForfaitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisieFicheController extends AbstractController
{
    #[Route('/saisiefiche', name: 'app_saisie_fiche', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $moisActuel = new \DateTime('first day of this month');

        $ficheFrais = $em->getRepository(FicheFrais::class)
            ->findOneBy([
                'user' => $user,
                'mois' => $moisActuel
            ]);

        if (!$ficheFrais) {
            $ficheFrais = new FicheFrais();
            $ficheFrais->setUser($this->getUser());
            $ficheFrais->setMois($moisActuel);
            $ficheFrais->setDateModif(new \DateTime());
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValid(0);
            $etat = $em->getRepository(Etat::class)->find(2);
            $ficheFrais->setEtat($etat);

            $fraisForfaitRepo = $em->getRepository(FraisForfait::class);

            $fraisKm = $fraisForfaitRepo->find(1);
            $fraisEtape = $fraisForfaitRepo->find(2);
            $fraisNuitee = $fraisForfaitRepo->find(3);
            $fraisRepas = $fraisForfaitRepo->find(4);


            $ligneFraisForfaitKm = new LigneFraisForfait();
            $ligneFraisForfaitKm->setFichesFrais($ficheFrais);
            $ligneFraisForfaitKm->setFraisForfaits($fraisKm);
            $ligneFraisForfaitKm->setQuantite(0);
            $em->persist($ligneFraisForfaitKm);

            $ligneFraisForfaitEtape = new LigneFraisForfait();
            $ligneFraisForfaitEtape->setFichesFrais($ficheFrais);
            $ligneFraisForfaitEtape->setFraisForfaits($fraisEtape);
            $ligneFraisForfaitEtape->setQuantite(0);
            $em->persist($ligneFraisForfaitEtape);

            $ligneFraisForfaitNuitee = new LigneFraisForfait();
            $ligneFraisForfaitNuitee->setFichesFrais($ficheFrais);
            $ligneFraisForfaitNuitee->setFraisForfaits($fraisNuitee);
            $ligneFraisForfaitNuitee->setQuantite(0);
            $em->persist($ligneFraisForfaitNuitee);

            $ligneFraisForfaitRepas = new LigneFraisForfait();
            $ligneFraisForfaitRepas->setFichesFrais($ficheFrais);
            $ligneFraisForfaitRepas->setFraisForfaits($fraisRepas);
            $ligneFraisForfaitRepas->setQuantite(0);
            $em->persist($ligneFraisForfaitRepas);

            $ficheFrais->addLignesFraisForfait($ligneFraisForfaitEtape);
            $ficheFrais->addLignesFraisForfait($ligneFraisForfaitKm);
            $ficheFrais->addLignesFraisForfait($ligneFraisForfaitNuitee);
            $ficheFrais->addLignesFraisForfait($ligneFraisForfaitRepas);

            $em->persist($ficheFrais);
            $em->flush();
        }

        $data = [];
        foreach ($ficheFrais->getLignesFraisForfait() as $ligne) {
            $type = $ligne->getFraisForfaits()->getId(); // Identifiant du type de frais
            switch ($type) {
                case 1:
                    $data['forfaitKm'] = $ligne->getQuantite();
                    break;
                case 2:
                    $data['forfaitEtape'] = $ligne->getQuantite();
                    break;
                case 3:
                    $data['forfaitNuitee'] = $ligne->getQuantite();
                    break;
                case 4:
                    $data['forfaitRepas'] = $ligne->getQuantite();
                    break;
            }
        }

        $formFraisForfaits = $this->createForm(SaisieFicheForfaitType::class, $data);
        $formFraisForfaits->handleRequest($request);

        if ($formFraisForfaits->isSubmitted() && $formFraisForfaits->isValid()) {

            $km = $formFraisForfaits->get('forfaitKm')->getData();
            $etape = $formFraisForfaits->get('forfaitEtape')->getData();
            $nuitee = $formFraisForfaits->get('forfaitNuitee')->getData();
            $repas = $formFraisForfaits->get('forfaitRepas')->getData();
            //dd($ficheFrais);
            $ficheFrais->getLignesFraisForfait()->get(0)->setQuantite($km);
            $ficheFrais->getLignesFraisForfait()->get(1)->setQuantite($etape);
            $ficheFrais->getLignesFraisForfait()->get(2)->setQuantite($nuitee);
            $ficheFrais->getLignesFraisForfait()->get(3)->setQuantite($repas);

            $ficheFrais->setDateModif(new \DateTime());

        }
        $em->persist($ficheFrais);
        $em->flush();

        $moisFormatte = $moisActuel->format('M Y');

        return $this->render('saisie_fiche/index.html.twig', [
            'formForfaits' => $formFraisForfaits->createView(),
            'moisActuel' => $moisFormatte
        ]);
    }
}
