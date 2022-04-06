<?php

namespace App\Controller;

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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\Event;

class AfficherSortieController extends AbstractController
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
     * @Route("/afficher/sortie/{id}", name="app_afficher_sortie")
     */
    public function afficher($id, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        //Affichage
        $sortie = $this->sortieRepo->find($id);
        $users = $sortie->getInscrit();
        $newNb = $sortie->getInscrit()->count();


        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'users' => $users,
            'nbInscrits' => $newNb,
            'formSortie' => $formSortie->createView()
        ]);
    }

    /**
     * @Route("/afficher/sortie/inscription/{id}", name="app_afficher_sortie_inscription")
     */
    public function inscription($id, EntityManagerInterface $em): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $sortie = $this->sortieRepo->find($id);

        //CHECK DATE FIN INSCRIPTION + NB INSCRIPTION MAX
        $time = new \DateTime();

        $nb = $sortie->getInscrit()->count();

        // INSCRIPTION
        if ($sortie->getDateFinInscription() <= $time) {
            $this->addFlash('warning', "La date de fin d'inscription est passé");
        } elseif ($sortie->getNbInscriptionsMax() < $nb) {
            $this->addFlash('warning', "La sortie est complète !");
        } else {
            $sortie->addInscrit($user);
        }

        //On rechek le nombre de participant
        $newNb = $sortie->getInscrit()->count();

        //si le nombre de participant est égale au nbMax de participant on change l'Etat à cloturée
        if ($sortie->getNbInscriptionsMax() == $newNb) {
            $etat = $this->etatRepo->find(3);
            $sortie->setEtat($etat);
        }

        $users = $sortie->getInscrit();

        $em->persist($sortie);
        $em->flush();
        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'nbInscrits' => $newNb,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/afficher/sortie/desister/{id}", name="app_afficher_sortie_desister")
     */
    public function desister($id, EntityManagerInterface $em): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $sortie = $this->sortieRepo->find($id);
        $users = $sortie->getInscrit();
        $newNb = $sortie->getInscrit()->count();

        //check user n'est pas l'organisateur
        if ($user->getId() == $sortie->getOrganisateur()->getId()) {
            $this->addFlash('warning', "Vous ne pouvez pas vous desister, vous devez annuler la sortie");
        } else {
            //CHECK date du jour > date limite
            $time = new \DateTime();
            if ($sortie->getDateDebut() < $time) {
                $this->addFlash('warning', "Il est trop tard pour se désister");
            } else {
                $sortie->removeInscrit($user);
                $nb = $sortie->getInscrit()->count();
                if ( $sortie->getEtat()== 3 and $sortie->getNbInscriptionsMax() > $nb) {
                    $etat = $this->etatRepo->find(2);
                    $sortie->setEtat($etat);
                }
            }

            $em->persist($sortie);
            $em->flush();
        }
        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'nbInscrits' => $newNb,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/afficher/sortie/publier/{id}", name="app_afficher_sortie_publier")
     */
    public function publier($id, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $sortie = $this->sortieRepo->find($id);
        $users = $sortie->getInscrit();
        $sortie->addInscrit($user);
        $newNb = $sortie->getInscrit()->count();

        $etat = $this->etatRepo->find(2);
        dump($etat);
        $sortie->setEtat($etat);
        dump($sortie);

        $em->persist($sortie);
        $em->flush();

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'nbInscrits' => $newNb,
            'users' => $users
        ]);
    }


    /**
     * @Route("/afficher/sortie/annuler/{id}", name="app_afficher_sortie_annuler")
     */
    public function annuler($id, Request $request, EntityManagerInterface $em): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $sortie = $this->sortieRepo->find($id);

        if (!$user->getId() == $sortie->getOrganisateur()->getId()) {
            return $this->redirectToRoute('app_accueil');
        }

        $users = $sortie->getInscrit();
        $nbInscrit = " ";
        //changement de l'etat --> ANNULER
        $etat = $this->etatRepo->find(6);
        $sortie->setEtat($etat);

        //Partie formulaire pour ajouter le motif d'annulation avec la fenêtre modal

        $motif = $request->request->get("motifAnnulation");
        $sortie->setMotifAnnulation($motif);


        $em->persist($sortie);
        $em->flush();

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'users' => $users,
            'nbInscrits' => $nbInscrit
        ]);

    }

    /**
     * @Route("/afficher/sortie/modifier/{id}", name="app_afficher_sortie_modifier")
     */
    public function modifier($id, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $sortie = $this->sortieRepo->find($id);

        if (!$user->getId() == $sortie->getOrganisateur()->getId()) {
            return $this->redirectToRoute('app_accueil');
        }

        $users = $sortie->getInscrit();
        $newNb = $sortie->getInscrit()->count();
        $lieuSortie = new Lieu();
        $lieuSortie->getId();
        $notif = "";
        $error = "";

        //Partie formulaire pour ajouter des lieux avec la fenêtre modal
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            if ($this->lieuRepo->findBy(['nom' => $lieu->getNom()])) {
                $notif = "Ce lieu existe déjà";
            } elseif ($this->lieuRepo->findBy(['rue' => $lieu->getRue()])) {
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
            if (is_int($ville->getCodePostal())) {
                $errorCpo = "Code Postal inconnu";
            } else {
                if ($this->villeRepo->findBy(['nom' => $ville->getNom()])) {
                    $notif = "Cette ville existe déjà";
                } else {
                    $notif = "Ville ajoutée";
                    $em->persist($ville);
                    $em->flush();
                }
            }
        }

        //check formulaire complet
        $formModifSortie = $this->createForm(SortieType::class, $sortie);
        $formModifSortie->handleRequest($request);

        if ($formModifSortie->isSubmitted() && $formModifSortie->isValid()) {
            if ($sortie->getDuree() > 2880) {
                $error = "La durée est trop longue (max 2880 min = 48h)";
            } else {
                //on vient ajouter en BDD
                $em->persist($sortie);
                $em->flush();

                return $this->redirect($this->generateUrl('app_afficher_sortie', ['id' => $sortie->getId()]));
            }
        }
        //on vient ajouter en BDD
        $em->persist($sortie);
        $em->flush();

        return $this->render('sortie/modifierSortie.html.twig', [
            'sortie' => $sortie,
            'users' => $users,
            "notif" => $notif,
            "error" => $error,
            "errorCpo" => $errorCpo,
            'lieu' => $lieuSortie,
            'nbInscrits' => $newNb,
            "locationForm" => $lieuForm->createView(),
            'formVille' => $formVille->createView(),
            'formModifSortie' => $formModifSortie->createView()
        ]);
    }
}