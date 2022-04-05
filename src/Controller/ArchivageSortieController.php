<?php

namespace App\Controller;

use App\Entity\ArchivesSorties;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArchivageSortieController extends AbstractController
{
    /**
     * @Route("/", name="app_archivage_sortie")
     */
    public function index(SortieRepository $repoSortie, EtatRepository $repoEtat, EntityManagerInterface $em): Response
    {

        // archivage des sorties terminees depuis plus de 30 jours
        $now = new \DateTime(date('Y-m-d H:i:s'));
        $dateArchivage = clone $now;
        $dateArchivage->sub(new \DateInterval('P30D'));
        $sorties = $repoSortie->findAArchiver($dateArchivage);

        foreach ($sorties as $sortie) {

            // creation de l'objet archive avec sauvegarde des informations a conserver
            $archiveSortie = new ArchivesSorties();
            $archiveSortie->setNomSortie($sortie->getNom());
            $archiveSortie->setDateDebut($sortie->getDateDebut());
            $archiveSortie->setDuree($sortie->getDuree());
            $archiveSortie->setDateFinInscription($sortie->getDateFinInscription());
            $archiveSortie->setNbInscriptionsMax($sortie->getNbInscriptionsMax());
            $archiveSortie->setDescription($sortie->getDescription());
            $archiveSortie->setPseudoOrganisateur($sortie->getOrganisateur()->getPseudo());
            $archiveSortie->setNomOrganisateur($sortie->getOrganisateur()->getNom());
            $archiveSortie->setPrenomOrganisateur($sortie->getOrganisateur()->getPrenom());
            $archiveSortie->setNomLieu($sortie->getLieu()->getNom());
            $archiveSortie->setNomVille($sortie->getLieu()->getVille()->getNom());
            $archiveSortie->setCpVille($sortie->getLieu()->getVille()->getCodePostal());
            $archiveSortie->setNbParticipants($repoSortie->countParticipants($sortie->getId()));

            // archivage en bdd
            $em->persist($archiveSortie);
            $em->flush();

            // suppression de la sortie archivee
            $em->remove($sortie);
            $em->flush();
        }

        // actualisation des sorties cloturees
        $sorties = $repoSortie->findACloturer($now);
        if ($sorties != null) {
            $etat = $repoEtat->find(3);
            foreach ($sorties as $sortie) {
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();
            }
        }

        // annulation des sorties creees non ouvertes a la fin des inscriptions
        $sorties = $repoSortie->findAAnnuler($now);
        if ($sorties != null) {
            $etat = $repoEtat->find(6);
            foreach ($sorties as $sortie) {
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();
            }
        }

        // actualisation des sorties en cours
        $sorties = $repoSortie->findEnCours($now);
        if ($sorties != null) {
            $etat = $repoEtat->find(4);
            foreach ($sorties as $sortie) {
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();
            }
        }

        // actualisation des sorties passees
        $sorties = $repoSortie->findPassees($now);
        if ($sorties != null) {
            $etat = $repoEtat->find(5);
            foreach ($sorties as $sortie) {
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();
            }
        }

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } else {
            return $this->redirectToRoute('app_accueil');
        }

//        return $this->render('archivage_sortie/index.html.twig', [
//            'controller_name' => 'salut'
//        ]);
    }
}
