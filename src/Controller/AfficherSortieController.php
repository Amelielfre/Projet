<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AfficherSortieController extends AbstractController
{
    /**
     * @Route("/afficher/sortie/{id}", name="app_afficher_sortie")
     */
    public function afficher($id, Request $request, SortieRepository $repoSortie): Response
    {

        $sortie = $repoSortie->find($id);

        return $this->render('sortie/afficherSortie.html.twig', [
            'controller_name' => 'AfficherSortieController',
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/inscription/sortie", name="app_inscription_sortie")
     */
    public function inscription(Request $request,SortieRepository $repoSortie): Response
    {




        return $this->render('sortie/afficherSortie.html.twig', [
            'controller_name' => 'AfficherSortieController',

        ]);
    }
}
