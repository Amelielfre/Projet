<?php

namespace App\Controller;

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
    public function afficher($id, Request $request, SortieRepository $repoSortie): Response
    {



        // INSCRIPTION
        $sortie = $repoSortie->find($id);
        $users = $this->getUser();
        $this->addFlash('succes', 'Votre inscription a bien été enregistrée !');

        return $this->render('sortie/afficherSortie.html.twig', [
            'controller_name' => 'AfficherSortieController',
            'users' => $users,
            'sortie' => $sortie
        ]);
    }
}
