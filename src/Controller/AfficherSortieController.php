<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\Event;

class AfficherSortieController extends AbstractController
{
    /**
     * @Route("/afficher/sortie/{id}", name="app_afficher_sortie")
     */
    public function afficher($id, Request $request, SortieRepository $repoSortie, UserRepository $userRepo): Response
    {
        //Affichage
        $sortie = $repoSortie->find($id);
        /*     if ($this->getUser()) {
                 $user = $this->getUser();
                 // INSCRIPTION
                 if ($request->request->get("inscription")) {
                     $sortie->addInscrit($user);
                     $this->addFlash('succes', 'Votre inscription a bien été enregistrée !');
                 }
             } else {
                 return $this->redirectToRoute('app_login');
             }*/
        $users = $sortie->getInscrit();
        return $this->render('sortie/afficherSortie.html.twig', [
            'users' => $users,
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/afficher/sortie/inscription/{id}", name="app_afficher_sortie_inscription")
     */
    public function inscription($id, SortieRepository $repoSortie, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        $sortie = $repoSortie->find($id);
        // $user->addSortiesInscrit($sortie);
        dump("oli");
        // INSCRIPTION
        //$user->addSortiesInscrit($sortie);
        dump("olo");
        $sortie->addInscrit($user);
        dump("ola");
        $this->addFlash('succes', 'Votre inscription a bien été enregistrée !');
        $em->flush();
        $users = $sortie->getInscrit();
        return $this->render('sortie/afficherSortie.html.twig', [
            'users' => $users,
            'sortie' => $sortie
        ]);
    }
}
