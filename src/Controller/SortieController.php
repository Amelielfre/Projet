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
    public function creationSortie(Request $request, EntityManagerInterface $em): Response
    {

        $sortie = new Sortie();
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
                $this->addFlash('warning', 'Ce lieu existe déjà');
            }elseif ($this->lieuRepo->findBy( ['rue' => $lieu->getRue()])){
                $this->addFlash('warning', 'Ce lieu existe déjà');
            } else {
                $this->addFlash('success', 'Lieu ajouté');
                $em->persist($lieu);
                $em->flush();
            }
        }

        $lieuForm = $this->createForm(LieuType::class);

        //Partie formulaire pour ajouter des villes avec la fenêtre modal
        $ville = new Ville();
        $formVille = $this->createForm(VilleType::class, $ville);
        $formVille->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()) {
            if ($this->villeRepo->findBy(['nom' => $ville->getNom()])) {
                $this->addFlash('warning', 'Cette ville existe déjà');
            } else {
                $this->addFlash('success', 'Ville ajouté');
                $em->persist($ville);
                $em->flush();
            }
        }

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        //on vérifie si le formulaire complet est submit et validé
        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            //si le user à cliqué sur enregistrer on vient ajouter l'etat "Créée" à la sortie
            if ($request->request->get("save")) {
                $etat = $this->etatRepo->find(1);
                $sortie->setEtat($etat);
            } else {
                //sinon l'état devient -> "en cours"
                $etat = $this->etatRepo->find(2);
                $sortie->setEtat($etat);
            }
            //on vient ajouter en BDD
            $em->persist($sortie);
            $em->flush();
            return $this->redirect($this->generateUrl('app_afficher_sortie', ['id' => $sortie->getId()]));
        }

        return $this->render('sortie/creation.html.twig', [
            "formSortie" => $formSortie->createView(),
            "locationForm" => $lieuForm->createView(),
            'formVille' => $formVille->createView()
        ]);

    }

}
