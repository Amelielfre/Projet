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
        $notif[] = null;
        $error = "";


        //vérification du user en session
        if ($this->getUser()) {

            //on récupère l'utilisateur connecté
            $user = $this->getUser();

            //on set l'organisteur de la sortie par le User qui est connecté
            $sortie->setOrganisateur($user);

            //on vient l'ajouter dans la table sortieId_userId en BDD pour
            //l'ajouter à la liste des inscrits
            $sortie->addInscrit($user);

            //on set le site orgranisteur par celui du User connecté
            $sortie->setSiteOrganisateur($this->getUser()->getSite());
        } else {
            //si l'utilisateur n'est pas connecté et qu'il
            // tente d'aller sur la page de création il est redirigé vers le login
            return $this->redirectToRoute('app_login');
        }

        //***************************************************************
        //Partie formulaire pour ajouter des lieux avec la fenêtre modal
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            //vérification des contraintes de sécurité
            if ($this->lieuRepo->findBy(['nom' => $lieu->getNom()])) {
                if ($this->lieuRepo->findBy(['rue' => $lieu->getRue()])) {
                    $notif["lieu"] = "Ce lieu existe déjà";
                }
            }

            if ($lieu->getNom() == null or $lieu->getRue() == null) {
                $notif["lieu"] = "Veuillez remplir les champs";
            }


            //on vient vérifier le nb d'erreurs et on vient ajouter en BDD
            if (count($notif) < 2) {
                $notif["lieu"] = "Lieu ajouté";
                $em->persist($lieu);
                $em->flush();
            }

        }
        $lieuForm = $this->createForm(LieuType::class);


        //***************************************************************
        //Partie formulaire pour ajouter des villes avec la fenêtre modal
        $ville = new Ville();
        $formVille = $this->createForm(VilleType::class, $ville);
        $formVille->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()) {

            //vérification des contraintes de sécurité
            if ($ville->getCodePostal() == null or $ville->getNom() == null) {
                $notif["ville"] = "Veuillez remplir les champs";
            }
            if (!is_numeric($ville->getCodePostal()) or strlen($ville->getCodePostal()) != 5) {
                $notif["ville"] = "Code Postal inconnu";
            }

            $ville1 = $this->villeRepo->findBy(['nom' => $ville->getNom()]);
            $ville2 = $this->villeRepo->findBy(['codePostal' => $ville->getCodePostal()]);

            if ($ville1 != null && $ville2 != null) {
                if ($ville1[0]->getId() == $ville2[0]->getId()) {
                    $notif["ville"] = "Cette ville existe déjà";
                }
            }

            //on vient vérifier le nb d'erreurs et on vient ajouter en BDD
            if (count($notif) < 2) {
                $notif["ville"] = "Ville ajoutée";
                $em->persist($ville);
                $em->flush();
            }
        }

        //***************************************************************
        //Partie du formulaire pour créer une sortie
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        //on vérifie si le formulaire complet est submit et validé
        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            if ($sortie->getDuree() > 2880) {
                $error = "La durée est trop longue (max 2880 min = 48h)";
            } else {
                //si le user a cliqué sur enregistrer on vient ajouter l'etat "Créée" à la sortie
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


        //Redirection vers la page de création sortie, avec tout les formulaires
        //ainsi que les potentielles erreurs
        return $this->render('sortie/creation.html.twig', [
            "notif" => $notif,
            "error" => $error,
            "formSortie" => $formSortie->createView(),
            "locationForm" => $lieuForm->createView(),
            'formVille' => $formVille->createView()
        ]);

    }

}
