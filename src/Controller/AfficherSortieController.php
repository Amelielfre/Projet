<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\Event;

class AfficherSortieController extends AbstractController
{
    /**
     * @Route("/afficher/sortie/{id}", name="app_afficher_sortie")
     */
    public function afficher($id, Request $request, SortieRepository $repoSortie, UserRepository $userRepo): Response
    {
        //Affichage
        $sortie = $repoSortie->find($id);
        $users = $sortie->getInscrit();

        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'users' => $users,
            'formSortie' => $formSortie->createView()
        ]);
    }

    /**
     * @Route("/afficher/sortie/inscription/{id}", name="app_afficher_sortie_inscription")
     */
    public function inscription($id, SortieRepository $repoSortie, EntityManagerInterface $em): Response
    {
        $inscritsSortie = array();
        $user = $this->getUser();
        $sortie = $repoSortie->find($id);

        //CHECK DATE FIN INSCRIPTION + NB INSCRIPTION MAX
        $time = new \DateTime();
        //    dump($time);
        //    dump($sortie->getDateFinInscription());
        $nb = $sortie->getInscrit()->count();
        //  dump($sortie->getNbInscriptionsMax());
        //  dump($sortie->getInscrit());
        //  dump($nb);
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
            'users' => $users,
        ]);
    }

    /**
     * @Route("/afficher/sortie/desister/{id}", name="app_afficher_sortie_desister")
     */
    public function desister($id, SortieRepository $repoSortie, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        $sortie = $repoSortie->find($id);
        $users = $sortie->getInscrit();
        //check user n'est pas l'organisateur
        if ($user->getId() == $sortie->getOrganisateur()->getId()) {
            $this->addFlash('warning', "Vous ne pouvez pas vous desister, vous devez annuler la sortie");
        } else {
            //CHECK date du jour > date limite
            $time = new \DateTime();
            if ($sortie->getDateDebut() < $time) {
                $this->addFlash('warning', "Il est trop tard pour se désister");
            }

            // DESISTER

            $sortie->removeInscrit($user);

            $em->persist($sortie);
            $em->flush();
        }
        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/afficher/sortie/publier/{id}", name="app_afficher_sortie_publier")
     */
    public function publier($id, EtatRepository $etatRepo, SortieRepository $sortieRepo, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $sortie = $sortieRepo->find($id);
        $users = $sortie->getInscrit();
        $sortie->addInscrit($user);

        $etat = $etatRepo->find(2);
        dump($etat);
        $sortie->setEtat($etat);
        dump($sortie);

        $em->persist($sortie);
        $em->flush();

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'users' => $users
        ]);
    }


    /**
     * @Route("/afficher/sortie/annuler/{id}", name="app_afficher_sortie_annuler")
     */
    public function annuler($id, EtatRepository $etatRepo, Request $request, SortieRepository $sortieRepo, EntityManagerInterface $em): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $sortie = $sortieRepo->find($id);
        $users = $sortie->getInscrit();
        //changement de l'etat --> ANNULER
        $etat = $etatRepo->find(6);
        dump($etat);
        $sortie->setEtat($etat);
        dump($sortie);

        //Partie formulaire pour ajouter le motif d'annulation avec la fenêtre modal

        $motif = $request->request->get("motifAnnulation");
        $sortie->setMotifAnnulation($motif);
        dump($motif);


        $em->persist($sortie);
        $em->flush();

        return $this->render('sortie/afficherSortie.html.twig', [
            'sortie' => $sortie,
            'users' => $users,
        ]);

    }
}