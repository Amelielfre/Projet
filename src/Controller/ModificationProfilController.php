<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModificationProfilType;

use App\Repository\UserRepository;
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
    /**
     * @Route("/modifier", name="modifier")
     */
    public function modifProfil(SluggerInterface $slugger,Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        dump($user);
        $errors[] = null;
        $user2 = new User();
        // CREATION FORMULAIRE*****
        $formModifProfil = $this->createForm(ModificationProfilType::class, $user);
        $formModifProfil->handleRequest($request);

        // traitement du formulaire ********
        // Récupère les données remplies dans les champs des formulaires
        $oldPassword = $formModifProfil->get("oldPassword")->getData();
        $password = $formModifProfil->get("password")->getData();
        $confirmPassword = $formModifProfil->get("confirm_password")->getData();

        if ($formModifProfil->isSubmitted() && $formModifProfil->isValid() && password_verify($oldPassword, $user->getPassword())) {
            if ($password != null && $confirmPassword != null) {

                // vérification de l'ancien mdp avec celui hashé en bdd + vérification du nouveau mdp confirmé
                if ($password == $confirmPassword) {
                    if ($oldPassword != $password) {

                        $user->setPassword(
                            $userPasswordHasher->hashPassword($user, $password)

                        );

                    } else {
                        $errors[] = "Votre mot de passe doit être différent de l'ancien";
                    }
                } else {
                    $errors[] = "Les nouveaux mots de passe ne sont pas identiques";
                }
            }
            // MODIFIER LA PHOTO
            $photo = $formModifProfil->get('photo')->getData();

            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);

                // this is needed to safely include the file name as part of the URL$safeFilename = $slugger->slug($originalFilename);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are storedtry {
                $photo->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $user->setUrlPhoto($newFilename);
            }
            // ENVOI EN BDD
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_profil_afficher', [
                'id' => $user->getId()
            ]);

        } else if ($formModifProfil->isSubmitted() && $formModifProfil->isValid() && !password_verify($oldPassword, $user->getPassword())) {
            $errors[] = "Pour modifier vos informations, vous devez rentrer votre mot de passe actuel";
            $errors[] = "Pour modifier votre mot de passe, vous devez saisir votre ancien mot de passe, puis le nouveau, puis le confirmer";
        }

        dump($user);

        return $this->render('modification_profil/modifProfil.html.twig', [
            'formModifProfil' => $formModifProfil->createView(),
            'user' => $user,
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/{id}", name="afficher")
     */
    public function afficherProfil($id, UserRepository $userRepo): Response
    {
        $user = $userRepo->find($id);
        dump($user);
        return $this->render('modification_profil/profil.html.twig', [
            'user' => $user
        ]);
    }
}
