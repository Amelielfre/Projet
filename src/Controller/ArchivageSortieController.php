<?php

namespace App\Controller;

use App\Entity\ArchivesSorties;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArchivageSortieController extends AbstractController
{
    /**
     * @Route("/archivage", name="app_archivage_sortie")
     */
    public function index(SortieRepository $repoSortie, EntityManagerInterface $em): Response
    {

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $date->sub(new \DateInterval('P30D'));
        $sorties = $repoSortie->findAArchiver($date);

        foreach ($sorties as $sortie) {
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

            $em->persist($archiveSortie);
            $em->flush();

    }

        return $this->render('archivage_sortie/index.html.twig', [
            'controller_name' => 'ArchivageSortieController',
        ]);
    }
}
