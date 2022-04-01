<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="app_sortie_")
 */
class SortieController extends AbstractController
{

    public function __construct(SortieRepository $sortieRepo, EtatRepository $etatRepo,
                                UserRepository $userRepo, VilleRepository $villeRepo, LieuRepository $lieuRepo)
    {
        $this->sortieRepo = $sortieRepo;
        $this->etatRepo = $etatRepo;
        $this->userRepo = $userRepo;
        $this->villeRepo = $villeRepo;
        $this->lieuRepo = $lieuRepo;
    }

    /**
     * @Route("/creation", name="creation")
     */
    public function creationSortie(Request $request, EntityManagerInterface $em): Response
    {

        $sortie = new Sortie();
        //vérification du user en session
        if ($this->getUser()) {
            //on récupère l'utilisateur connecté
            $user = $this->getUser();
            $sortie->setOrganisateur($user);
            $sortie->addInscrit($user);
            $sortie->setSiteOrganisateur($this->getUser()->getSite());
        } else {
            return $this->redirectToRoute('app_login');
        }

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            if ($request->request->get("save")) {
                $etat = $this->etatRepo->find(1);
                $sortie->setEtat($etat);
            } else {
                $etat = $this->etatRepo->find(2);
                $sortie->setEtat($etat);
            }
            $em->persist($sortie);
            $em->flush();
            return $this->redirect($this->generateUrl('app_afficher_sortie', ['id' => $sortie->getId()]));
        }

         $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $em->persist($lieu);
            $em->flush();
        }

        $lieuForm = $this->createForm(LieuType::class);

        return $this->render('sortie/creation.html.twig', [
            "formSortie" => $formSortie->createView(),
            "locationForm" => $lieuForm->createView()
        ]);

    }

}
