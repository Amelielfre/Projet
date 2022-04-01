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
        if ($sortie->getInscrit()){
            $users = $sortie->getInscrit();
            return $this->render('sortie/afficherSortie.html.twig', [
                'users' => $users,
                'sortie' => $sortie
            ]);
        }
        return $this->render('sortie/afficherSortie.html.twig', [
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

        // INSCRIPTION

        $sortie->addInscrit($user);
        if ($sortie->getInscrit()){
            $users = $sortie->getInscrit();
            return $this->render('sortie/afficherSortie.html.twig', [
                'users' => $users,
                'sortie' => $sortie
            ]);
        }
        $em->flush();
        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/afficher/sortie/desister/{id}", name="app_afficher_sortie_desister")
     */
    public function desister($id, SortieRepository $repoSortie, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        $sortie = $repoSortie->find($id);

        // DESISTER

        $sortie->removeInscrit($user);
        if ($sortie->getInscrit()){
            $users = $sortie->getInscrit();
            return $this->render('sortie/afficherSortie.html.twig', [
                'users' => $users,
                'sortie' => $sortie
            ]);
        }
        $em->flush();
        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie
        ]);
    }
}
