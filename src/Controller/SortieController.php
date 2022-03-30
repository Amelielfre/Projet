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
        if ($this->getUser()){

            //on récupère le pseudo de l'utilisateur connecté
//            $user->setPseudo($this->getUser()->getPseudo());
            $user = (object) $this->userRepo->findBy(["pseudo" => $this->getUser()->getPseudo()]);
//            dump($this->getUser()->getId());
//            $idUser = $this->getUser()->getId();
//
//            $user = new User();
//            $user->setId($idUser);

            $sortie->setOrganisateur($user);
            $sortie->setSiteOrganisateur($this->getUser()->getSite());
        }

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if($formSortie->isSubmitted() && $formSortie->isValid()){
            $etat = $this->etatRepo->find(1);

            dump($etat);
            $sortie->setEtat($etat);

            dump($sortie);
            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('app_sortie_creation');
        }

        dump($sortie);
        return $this->render('sortie/creation.html.twig', ["formSortie" => $formSortie->createView()]);
    }
}
