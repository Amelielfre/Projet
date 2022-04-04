<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="app_sortie_")
 */
class SortieController extends AbstractController
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
     * @Route("/creation", name="creation")
     */
    public function creationSortie(Request $request, EntityManagerInterface $em
    ): Response
    {
        $sortie = new Sortie();
        $notif = "";
        $error = "";
        //vérification du user en session
        if ($this->getUser()) {
            //on récupère l'utilisateur connecté
            $user = $this->getUser();
            $sortie->setOrganisateur($user);

            //on vient l'ajouter dans la table sortieId_userId en BDD
            $sortie->addInscrit($user);

            $sortie->setSiteOrganisateur($this->getUser()->getSite());
        } else {
            //si l'utilisateur qui n'est pas co
            // tente d'aller sur la page de création il est redirigé vers le login
            return $this->redirectToRoute('app_login');
        }

        //Partie formulaire pour ajouter des lieux avec la fenêtre modal
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);


        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            if ($this->lieuRepo->findBy(['nom' => $lieu->getNom()])){
                $notif = "Ce lieu existe déjà";
            }elseif ($this->lieuRepo->findBy( ['rue' => $lieu->getRue()])){
                $notif = "Ce lieu existe déjà";
            } else {
                $notif = "Lieu ajouté";
                $em->persist($lieu);
                $em->flush();
            }
        }

        $lieuForm = $this->createForm(LieuType::class);

        //Partie formulaire pour ajouter des villes avec la fenêtre modal
        $ville = new Ville();
        $errorCpo = "";
        $formVille = $this->createForm(VilleType::class, $ville);
        $formVille->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()) {
            if(!is_int($ville->getCodePostal())){
                $errorCpo = "Code Postal inconnu";
            }else {
                if ($this->villeRepo->findBy(['nom' => $ville->getNom()])) {
                    $notif = "Cette ville existe déjà";
                } else {
                    $notif = "Ville ajoutée";
                    $em->persist($ville);
                    $em->flush();
                }
            }
        }

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        //on vérifie si le formulaire complet est submit et validé
        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            if($sortie->getDuree()>2880){
                $error = "La durée est trop longue (max 2880 min = 48h)";
            } else if (is_int($sortie->getNbInscriptionsMax())){
                $error = "Le nombre de participant est inconnu";
            } else {
                //si le user à cliqué sur enregistrer on vient ajouter l'etat "Créée" à la sortie
                if ($request->request->get("save")) {
                    $etat = $this->etatRepo->find(1);
                    $sortie->setEtat($etat);
                } else {
                    //sinon l'état devient -> "Ouvert"
                    $etat = $this->etatRepo->find(2);
                    $sortie->setEtat($etat);
                }
                //on vient ajouter en BDD
                $em->persist($sortie);
                $em->flush();
                return $this->redirect($this->generateUrl('app_afficher_sortie', ['id' => $sortie->getId()]));
            }

        }

        return $this->render('sortie/creation.html.twig', [
            "notif" => $notif,
            "errorCpo" => $errorCpo,
            "error" => $error,
            "formSortie" => $formSortie->createView(),
            "locationForm" => $lieuForm->createView(),
            'formVille' => $formVille->createView()
        ]);

    }

}
