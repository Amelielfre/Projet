<?php

namespace App\Controller;

use App\Form\FiltresFormType;
use App\Repository\SortieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/accueil", name="app_accueil")
     */
    public function main(Request $request, SortieRepository $repoSortie): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $sorties = null;

        // CREATION FORMULAIRE
        $formFiltres = $this->createForm(FiltresFormType::class);
        $formFiltres->handleRequest($request);

        //traitement du formulaire
        if ($formFiltres->isSubmitted() && $formFiltres->isValid()) {
            $site = $formFiltres->get("site")->getData();
            dump($site);
            $dateDebut = $formFiltres->get("dateDebut")->getData();
            dump($dateDebut);
            $dateFin = $formFiltres->get("dateFin")->getData();
            dump($dateFin);
            $sorties = $repoSortie->findByFiltres($site, $dateDebut, $dateFin);
        }
        dump($sorties);
        if ($sorties == null) {
            $sorties = $repoSortie->findAll();
        }

        return $this->render('main/accueil.html.twig', [
            'formFiltres' => $formFiltres->createView(),
            'sorties' => $sorties
        ]);
    }
}
