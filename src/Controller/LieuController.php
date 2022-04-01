<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu/ajouter", name="app_ajouter_lieu")
     */
    public function index(Request $request, VilleRepository $villeRepo, EntityManagerInterface $em): Response
    {

        //récupère les données POST
        $locationData = $request->request->get('location');

        //récupère les infos de la ville associée à ce lieu
        $ville = $villeRepo->find($locationData["ville"]);

        //@TODO: gérer si on ne trouve pas la ville

        //instancie notre Location et l'hydrate avec les données reçues
        $lieu = new Lieu();
        $lieu->setLieu($ville);
        $lieu->setNom($locationData["name"]);
        $lieu->setRue($locationData["street"]);


        //sauvegarde en bdd
        $em->persist($lieu);
        $em->flush();

        //les données à renvoyer au code JS
        //status est arbitraire... mais je prend pour acquis que je renverrais toujours cette clé
        //avec comme valeur soit "ok", soit "error", pour aider le traitement côté client
        //je renvois aussi la Location. Pour que ça marche, j'ai implémenté \JsonSerializable dans l'entité, sinon c'est vide
        $data = [
            "status" => "ok",
            "location" => $lieu
        ];

        //renvoie la réponse sous forme de données JSON
        //le bon Content-Type est automatiquement configuré par cet objet JsonResponse
        return new JsonResponse($data);
    }
}
