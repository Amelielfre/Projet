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
//        $sorties = null;
//        $sorties = $repoSortie->findAll();

        // CREATION FORMULAIRE
        $formFiltres = $this->createForm(FiltresFormType::class);
        $formFiltres->handleRequest($request);

        //traitement du formulaire
        if ($formFiltres->isSubmitted() && $formFiltres->isValid()) {
            $dateDebut = $formFiltres->get("dateDebut")->getData();
            $dateFin = $formFiltres->get("dateFin")->getData();
            dump($dateDebut);
            dump($dateFin);
            $sorties = $repoSortie->findByDates($dateDebut, $dateFin);
            dump($sorties);
            return $this->render('main/accueil.html.twig', [
                'formFiltres' => $formFiltres->createView(),
                'sorties' => $sorties
            ]);
        }
        return $this->render('main/accueil.html.twig', [
            'formFiltres' => $formFiltres->createView(),
            'sorties' => $repoSortie->findAll() //TODO à revoir après
        ]);
    }
}
