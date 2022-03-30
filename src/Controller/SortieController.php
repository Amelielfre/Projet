<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="app_sortie_")
 */
class SortieController extends AbstractController
{

    public function __construct(SortieRepository $sortieRepo, EtatRepository $etatRepo, UserRepository $userRepo)
    {
        $this->sortieRepo = $sortieRepo;
        $this->etatRepo = $etatRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @Route("/creation", name="creation")
     */
    public function creationSortie(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        //vérification du user en session
        if ($this->getUser()){
            //on récupère l'utilisateur connecté
            $user = $this->getUser();
            $sortie->setOrganisateur($user);
            $sortie->setSiteOrganisateur($this->getUser()->getSite());
        } else {
            return $this->redirectToRoute('app_login');
        }

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if($formSortie->isSubmitted() && $formSortie->isValid()){
            $etat = $this->etatRepo->find(1);
            $sortie->setEtat($etat);

            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('app_sortie_creation');
        }

        dump($sortie);
        return $this->render('sortie/creation.html.twig', ["formSortie" => $formSortie->createView()]);
    }
}
