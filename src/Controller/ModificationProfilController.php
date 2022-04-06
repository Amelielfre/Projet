<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModificationProfilType;

use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/profil", name="app_profil_")
 */
class ModificationProfilController extends AbstractController
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
     * @Route("/modifier", name="modifier")
     */
    public function modifProfil( SluggerInterface $slugger, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $errors[] = null;
        $oldPseudo = $this->getUser()->getPseudo();
        $oldEmail = $this->getUser()->getEmail();
        // CREATION FORMULAIRE*****
        $formModifProfil = $this->createForm(ModificationProfilType::class, $user);
        $formModifProfil->handleRequest($request);


        // traitement du formulaire ********
        // Récupère les données remplies dans les champs des formulaires
        $oldPassword = $formModifProfil->get("oldPassword")->getData();
        $password = $formModifProfil->get("password")->getData();
        $confirmPassword = $formModifProfil->get("confirm_password")->getData();
        if($user->getEmail() != $oldEmail){
            if($this->userRepo->findBy(["email"=>$user->getEmail()])){
                $errors["email"] = "Email déjà existant";
            }
        }
        if ($formModifProfil->isSubmitted() && $formModifProfil->isValid() && password_verify($oldPassword, $user->getPassword())) {
            if($user->getPseudo() != $oldPseudo){
                if($this->userRepo->findBy(["pseudo"=>$user->getPseudo()])){
                    $errors["pseudo"] = "Pseudo déjà existant";
                }
            }


            if ($password != null && $confirmPassword != null) {
                // vérification de l'ancien mdp avec celui hashé en bdd + vérification du nouveau mdp confirmé
                if ($password == $confirmPassword) {
                    if ($oldPassword != $password) {

                        $user->setPassword(
                            $userPasswordHasher->hashPassword($user, $password)

                        );
                    } else {
                        $errors["mdpDiffAncien"] = "Votre mot de passe doit être différent de l'ancien";
                    }
                } else {
                    $errors["mdpDiff"] = "Les nouveaux mots de passe ne sont pas identiques";
                }
            }
            // MODIFIER LA PHOTO
            $photo = $formModifProfil->get('photo')->getData();

            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL$safeFilename = $slugger->slug($originalFilename);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                // Move the file to the directory where brochures are storedtry {
                $photo->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $user->setUrlPhoto($newFilename);
            }

            if(count($errors) < 2){
                // ENVOI EN BDD
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('app_profil_afficher', [
                    'id' => $user->getId()
                ]);
            }

        } else if ($formModifProfil->isSubmitted() && $formModifProfil->isValid() && !password_verify($oldPassword, $user->getPassword())) {
            $errors["mdpVerifModif"] = "Pour modifier vos informations, vous devez rentrer votre mot de passe actuel";
            $errors["mdpVerifModif2"] = "Pour modifier votre mot de passe, vous devez saisir votre ancien mot de passe, puis le nouveau, puis le confirmer";
        }
        return $this->render('modification_profil/modifProfil.html.twig', [
            'formModifProfil' => $formModifProfil->createView(),
            'user' => $user,
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/{id}", name="afficher")
     */
    public function afficherProfil($id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->userRepo->find($id);

        return $this->render('modification_profil/profil.html.twig', [
            'user' => $user
        ]);
    }
}
