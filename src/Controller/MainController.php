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

        $sorties = $repoSortie->findAccueil();
        $user = $this->getUser();

        // CREATION FORMULAIRE
        $formFiltres = $this->createForm(FiltresFormType::class);
        $formFiltres->handleRequest($request);

        //traitement du formulaire
        if ($formFiltres->isSubmitted() && $formFiltres->isValid()) {
            // recuperation des champs de filtres
            $site = $formFiltres->get("site")->getData();
            $motCles = $formFiltres->get("motCles")->getData();
            $dateDebut = $formFiltres->get("dateDebut")->getData();
            $dateFin = $formFiltres->get("dateFin")->getData();
            $orga = $formFiltres->get("orga")->getData();
            $inscrit = $formFiltres->get("inscrit")->getData();
            $pasInscrit = $formFiltres->get("pasInscrit")->getData();
            $passees = $formFiltres->get("passees")->getData();

            //execution de la requete
            $sorties = $repoSortie->findByFiltres($site, $user, $orga, $inscrit, $pasInscrit, $passees, $motCles, $dateDebut, $dateFin);
        }

        return $this->render('main/accueil.html.twig', [
            'formFiltres' => $formFiltres->createView(),
            'sorties' => $sorties
        ]);
    }
}
