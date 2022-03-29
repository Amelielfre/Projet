<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="app_sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/creation", name="creation")
     */
    public function creationSortie(Request $request): Response
    {
        $sortie = new Sortie();

//        $sortie->setSiteOrganisateur($this->getUser()->getSite()->getNom());

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        return $this->render('sortie/creation.html.twig', ["formSortie" => $formSortie->createView()]);
    }
}
