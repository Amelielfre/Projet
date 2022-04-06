<?php

namespace App\Controller;

use App\Form\FiltresFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    public function __construct(SortieRepository $sortieRepo, EtatRepository $etatRepo,
                                UserRepository   $userRepo, VilleRepository $villeRepo, LieuRepository $lieuRepo)
    {
        $this->sortieRepo = $sortieRepo;
        $this->etatRepo = $etatRepo;
        $this->userRepo = $userRepo;
        $this->villeRepo = $villeRepo;
        $this->lieuRepo = $lieuRepo;
    }

    /**
     * @Route("/accueil", name="app_accueil")
     */
    public function main(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $sorties = $this->sortieRepo->findAccueil();
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
            $sorties = $this->sortieRepo->findByFiltres($site, $user, $orga, $inscrit, $pasInscrit, $passees, $motCles, $dateDebut, $dateFin);
            dump($sorties);
        }

        return $this->render('main/accueil.html.twig', [
            'formFiltres' => $formFiltres->createView(),
            'sorties' => $sorties
        ]);
    }

    /**
     * @Route("/check", name="app_check")
     */
    public function check(){

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if(!$this->getUser()->getActif()){
            return $this->redirectToRoute('app_logout');
        } else {
            return $this->redirectToRoute('app_archivage_sortie');
        }
    }
}
