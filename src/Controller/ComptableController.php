<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use App\Form\ComptableSuiviPaiementFormType;
use App\Form\ComptableValidationFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comptable')]
#[IsGranted('ROLE_COMPTABLE')]
class ComptableController extends AbstractController
{
    #[Route('/', name: 'comptable')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $visiteur = null;
        $selectedFicheFrais = null;
        $ligneFraisForfait = null;
        $ligneFraisHorsForfait = null;

        // Étape 1: Vérifier si un visiteur est sélectionné
        if ($request->query->has('visiteur_id')) {
            $visiteur = $em->getRepository(User::class)->find($request->query->get('visiteur_id'));
        }

        // Étape 2: Vérifier si une fiche spécifique est demandée
        if ($request->query->has('fiche_id')) {
            $selectedFicheFrais = $em->getRepository(FicheFrais::class)->find($request->query->get('fiche_id'));

            if ($selectedFicheFrais) {
                $ligneFraisForfait = $selectedFicheFrais->getLignesFraisForfait();
                $ligneFraisHorsForfait = $selectedFicheFrais->getLignesFraisHorsForfait();
                // S'assurer que le visiteur correspond à la fiche
                if (!$visiteur) {
                    $visiteur = $selectedFicheFrais->getUser();
                }
            }
        }

        // Créer le formulaire avec ou sans visiteur préselectionné
        $form = $this->createForm(ComptableValidationFormType::class, null, [
            'visiteur' => $visiteur
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Si nous n'avons pas encore de visiteur ou si l'utilisateur en a sélectionné un nouveau
            if (!$visiteur || $visiteur->getId() !== $data['visiteur']->getId()) {
                // Rediriger vers la même page avec le visiteur sélectionné
                return $this->redirectToRoute('comptable', [
                    'visiteur_id' => $data['visiteur']->getId()
                ]);
            }

            // Si nous avons un visiteur et qu'un mois a été sélectionné
            if (isset($data['mois'])) {
                $selectedFicheFrais = $data['mois'];
                $ligneFraisForfait = $selectedFicheFrais->getLignesFraisForfait();
                $ligneFraisHorsForfait = $selectedFicheFrais->getLignesFraisHorsForfait();
                $this->addFlash('success', 'Fiche de frais trouvée!');
            }
        }

        return $this->render('comptable/index.html.twig', [
            'form' => $form->createView(),
            'selectedFicheFrais' => $selectedFicheFrais,
            'ligneFraisForfait' => $ligneFraisForfait,
            'ligneFraisHorsForfait' => $ligneFraisHorsForfait,
            'visiteur' => $visiteur,
        ]);
    }

    #[Route('/valider/{id}', name: 'comptable_valider_fiche')]
    public function validerFiche(FicheFrais $ficheFrais, EntityManagerInterface $em): Response
    {
        // Récupérer l'état "VA" (Validée) depuis la base de données
        $etatValide = $em->getRepository(Etat::class)->findOneBy(['id' => '4']);

        if (!$etatValide) {
            $this->addFlash('error', 'État "VA" introuvable dans la base de données');
            return $this->redirectToRoute('comptable');
        }

        // Logique pour valider une fiche
        $ficheFrais->setEtat($etatValide); // VA = Validée
        $ficheFrais->setDateModif(new \DateTime()); // Mise à jour de la date de modification
        $em->flush();

        $this->addFlash('success', 'La fiche a été validée avec succès');
        return $this->redirectToRoute('comptable', [
            'visiteur_id' => $ficheFrais->getUser()->getId(),
            'fiche_id' => $ficheFrais->getId()
        ]);
    }

//    #[Route('/rejeter/{id}', name: 'comptable_rejeter_fiche')]
//    public function rejeterFiche(FicheFrais $ficheFrais, EntityManagerInterface $em): Response
//    {
//        // Récupérer l'état "RE" (Refusée) depuis la base de données
//        $etatRefuse = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'RE']);
//
//        if (!$etatRefuse) {
//            $this->addFlash('error', 'État "RE" introuvable dans la base de données');
//            return $this->redirectToRoute('comptable');
//        }
//
//        // Logique pour rejeter une fiche
//        $ficheFrais->setEtat($etatRefuse); // RE = Refusée
//        $ficheFrais->setDateModif(new \DateTime()); // Mise à jour de la date de modification
//        $em->flush();
//
//        $this->addFlash('success', 'La fiche a été rejetée');
//        return $this->redirectToRoute('comptable', [
//            'visiteur_id' => $ficheFrais->getUser()->getId(),
//            'fiche_id' => $ficheFrais->getId()
//        ]);
//    }

    #[Route('/comptable_actualiser_forfait/{id}', name: 'comptable_actualiser_forfait')]
    public function actualiserForfait(Request $request, FicheFrais $ficheFrais, EntityManagerInterface $em): Response
    {
        // Récupération des données du formulaire - correction pour éviter l'erreur
        $fraisData = [];
        if ($request->request->has('frais')) {
            $fraisData = $request->request->all('frais');
        }

        if (!empty($fraisData)) {
            $ligneFraisForfaits = $ficheFrais->getLignesFraisForfait();

            // Mise à jour des quantités de frais forfaitisés
            foreach ($ligneFraisForfaits as $ligne) {
                $id = $ligne->getId();
                if (isset($fraisData[$id]) && is_numeric($fraisData[$id])) {
                    $ligne->setQuantite((int)$fraisData[$id]);
                }
                $em->persist($ligne);
            }

            // Mise à jour de la date de modification
            $ficheFrais->setDateModif(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Les frais forfaitisés ont été actualisés avec succès');
        }

        // Redirection vers la page du comptable avec la même fiche sélectionnée
        return $this->redirectToRoute('comptable', [
            'visiteur_id' => $ficheFrais->getUser()->getId(),
            'fiche_id' => $ficheFrais->getId()
        ]);
    }

    #[Route('/comptable_refuser_hors_forfait/{id}', name: 'comptable_refuser_hors_forfait')]
    public function refuserFraisHorsForfait(LigneFraisHorsForfait $ligneFraisHorsForfait, EntityManagerInterface $em): Response
    {
        $ficheFrais = $ligneFraisHorsForfait->getFicheFrais();

        // Préfixer le libellé avec "REFUSE: "
        $nouveauLibelle = "REFUSE: " . $ligneFraisHorsForfait->getLibelle();

        // Vérifier si le libellé ne dépasse pas la taille maximale du champ
        // Supposons que la taille maximale soit de 100 caractères (à adapter selon votre schéma de base de données)
        $maxLength = 100;
        if (strlen($nouveauLibelle) > $maxLength) {
            $nouveauLibelle = substr($nouveauLibelle, 0, $maxLength);
        }

        $ligneFraisHorsForfait->setLibelle($nouveauLibelle);

        // Mise à jour de la date de modification de la fiche
        $ficheFrais->setDateModif(new \DateTime());
        $em->flush();

        $this->addFlash('success', 'Le frais hors forfait a été refusé');

        // Rediriger vers la page principale avec les paramètres pour retrouver la même fiche
        return $this->redirectToRoute('comptable', [
            'visiteur_id' => $ficheFrais->getUser()->getId(),
            'fiche_id' => $ficheFrais->getId()
        ]);
    }

    #[Route('/comptable_reporter_hors_forfait/{id}', name: 'comptable_reporter_hors_forfait')]
    public function reporterFraisHorsForfait(LigneFraisHorsForfait $ligneFraisHorsForfait, EntityManagerInterface $em): Response
    {
        $ficheFrais = $ligneFraisHorsForfait->getFicheFrais();
        $visiteur = $ficheFrais->getUser();

        // Calculer le mois suivant
        $moisCourant = clone $ficheFrais->getMois();
        $moisSuivant = clone $moisCourant;
        $moisSuivant->modify('+1 month');

        // Vérifier si une fiche existe déjà pour le mois suivant
        $ficheSuivante = $em->getRepository(FicheFrais::class)->findOneBy([
            'user' => $visiteur,
            'mois' => $moisSuivant,
        ]);

        // Si pas de fiche pour le mois suivant, on en crée une nouvelle
        if (!$ficheSuivante) {
            $ficheSuivante = new FicheFrais();
            $ficheSuivante->setUser($visiteur);
            $ficheSuivante->setMois($moisSuivant);
            $ficheSuivante->setNbJustificatifs(0);
            $ficheSuivante->setMontantValid(0);
            $ficheSuivante->setDateModif(new \DateTime());

            // Récupérer l'état "CR" (Créée) depuis la base de données
            $etatCree = $em->getRepository(Etat::class)->findOneBy(['id' => '2']);

            if (!$etatCree) {
                $this->addFlash('error', 'État "CR" introuvable dans la base de données');
                return $this->redirectToRoute('comptable', [
                    'visiteur_id' => $ficheFrais->getUser()->getId(),
                    'fiche_id' => $ficheFrais->getId()
                ]);
            }

            $ficheSuivante->setEtat($etatCree); // CR = Créée (Saisie en cours)
            $em->persist($ficheSuivante);
        }

        // Créer une nouvelle ligne de frais hors forfait pour le mois suivant
        $nouvelleLigne = new LigneFraisHorsForfait();
        $nouvelleLigne->setFicheFrais($ficheSuivante);

        $nouveauLibelle = "REPORTE: " . $ligneFraisHorsForfait->getLibelle();

        // Vérifier si le libellé ne dépasse pas la taille maximale du champ
        $maxLength = 100; // Adapter selon votre schéma de base de données
        if (strlen($nouveauLibelle) > $maxLength) {
            $nouveauLibelle = substr($nouveauLibelle, 0, $maxLength);
        }

        $nouvelleLigne->setLibelle($nouveauLibelle);
        $nouvelleLigne->setDate($ligneFraisHorsForfait->getDate());
        $nouvelleLigne->setMontant($ligneFraisHorsForfait->getMontant());

        $em->persist($nouvelleLigne);

        // Supprimer la ligne de frais hors forfait du mois courant
        $em->remove($ligneFraisHorsForfait);

        // Mise à jour des dates de modification
        $ficheFrais->setDateModif(new \DateTime());
        $ficheSuivante->setDateModif(new \DateTime());

        $em->flush();

        $this->addFlash('success', 'Le frais hors forfait a été reporté au mois suivant');

        // Rediriger vers la page principale avec les paramètres pour retrouver la même fiche
        return $this->redirectToRoute('comptable', [
            'visiteur_id' => $ficheFrais->getUser()->getId(),
            'fiche_id' => $ficheFrais->getId()
        ]);
    }

    #[Route('/suivi-paiement', name: 'comptable_suivi_paiement')]
    public function suiviPaiement(Request $request, EntityManagerInterface $em): Response
    {
        $visiteur = null;
        $selectedFicheFrais = null;
        $ligneFraisForfait = null;
        $ligneFraisHorsForfait = null;

        // Création d'un formulaire similaire à celui de la validation
        // mais qui ne récupère que les fiches validées ou mises en paiement
        $form = $this->createForm(ComptableSuiviPaiementFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (isset($data['ficheFrais'])) {
                $selectedFicheFrais = $data['ficheFrais'];
                $ligneFraisForfait = $selectedFicheFrais->getLignesFraisForfait();
                $ligneFraisHorsForfait = $selectedFicheFrais->getLignesFraisHorsForfait();
                $this->addFlash('success', 'Fiche de frais trouvée!');
            }
        }

        return $this->render('comptable/suivi_paiement.html.twig', [
            'form' => $form->createView(),
            'selectedFicheFrais' => $selectedFicheFrais,
            'ligneFraisForfait' => $ligneFraisForfait,
            'ligneFraisHorsForfait' => $ligneFraisHorsForfait,
        ]);
    }

    #[Route('/mettre-en-paiement/{id}', name: 'comptable_mettre_en_paiement')]
    public function mettreEnPaiement(FicheFrais $ficheFrais, EntityManagerInterface $em): Response
    {
        // Récupérer l'état "RB" (Remboursée) puisque nous sautons l'étape "Mise en paiement"
        $etatRemboursee = $em->getRepository(Etat::class)->findOneBy(['id' => '3']);

        if (!$etatRemboursee) {
            $this->addFlash('error', 'État "Remboursée" introuvable dans la base de données');
            return $this->redirectToRoute('comptable_suivi_paiement');
        }

        // Logique pour marquer directement comme remboursée
        $ficheFrais->setEtat($etatRemboursee);
        $ficheFrais->setDateModif(new \DateTime());
        $em->flush();

        $this->addFlash('success', 'La fiche a été remboursée');
        return $this->redirectToRoute('comptable_suivi_paiement');
    }

    #[Route('/marquer-remboursee/{id}', name: 'comptable_marquer_remboursee')]
    public function marquerRemboursee(FicheFrais $ficheFrais, EntityManagerInterface $em): Response
    {
        // Récupérer l'état "RB" (Remboursée)
        $etatRemboursee = $em->getRepository(Etat::class)->findOneBy(['id' => '3']); // ID 3 selon votre ImportController

        if (!$etatRemboursee) {
            $this->addFlash('error', 'État "Remboursée" introuvable dans la base de données');
            return $this->redirectToRoute('comptable_suivi_paiement');
        }

        // Logique pour marquer une fiche comme remboursée
        $ficheFrais->setEtat($etatRemboursee);
        $ficheFrais->setDateModif(new \DateTime());
        $em->flush();

        $this->addFlash('success', 'La fiche a été marquée comme remboursée');
        return $this->redirectToRoute('comptable_suivi_paiement', [
            'ficheFrais' => $ficheFrais->getId()
        ]);
    }
}