<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
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
     * @Route("/ajax/rechercheLieuByVille", name="ajax_lieu_by_ville")
     */
    public function rechercheLieuByVille(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $lieux = $this->lieuRepo->findBy(['ville' => $request->query->get('ville_id')]);
        $json_data = array();
        $i = 0;
        if (sizeof($lieux) > 0) {
            foreach ($lieux as $lieu) {
                $json_data[$i++] = array('id' => $lieu->getId(), 'nom' => $lieu->getNom());
            }
            return new JsonResponse($json_data);
        } else {
            $json_data[$i++] = array('id' => '', 'nom' => 'Pas de lieu correspondant à votre recherche.');
            return new JsonResponse($json_data);
        }
    }
}
