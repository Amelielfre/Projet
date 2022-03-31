<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax/rechercheLieuByVille", name="ajax_lieu_by_ville")
     */
    public function rechercheLieuByVille(LieuRepository $lieuRepo, Request $request): Response
    {

        $lieux = $lieuRepo->findBy(['ville' => $request->query->get('ville_id')]);
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
