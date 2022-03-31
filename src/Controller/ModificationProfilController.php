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

/**
 * @Route("/profil", name="app_profil_")
 */
class ModificationProfilController extends AbstractController
{
    /**
     * @Route("/modifier", name="modifier")
     */
    public function modifProfil(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        $errors[] = null;

        // CREATION FORMULAIRE
        $formModifProfil = $this->createForm(ModificationProfilType::class, $user);
        $formModifProfil->handleRequest($request);

        // traitement du formulaire
        $oldPassword = $formModifProfil->get("oldPassword")->getData();
        $password = $formModifProfil->get("password")->getData();
        $confirmPassword = $formModifProfil->get("confirm_password")->getData();

        if ($formModifProfil->isSubmitted() && $formModifProfil->isValid()) {
            if ($oldPassword != null && $password != null && $confirmPassword != null) {

                // vérification de l'ancien mdp avec celui hashé en bdd + vérification du nouveau mdp confirmé
                if (password_verify($oldPassword, $user->getPassword()) && $password == $confirmPassword) {
                    if ($oldPassword != $password) {
                        $user->setPassword(
                            $userPasswordHasher->hashPassword($user, $password)
                        );
                        $em->persist($user);
                        $em->flush();
                        return $this->redirectToRoute('app_profil_afficher', [
                            'id' => $user->getId()
                        ]);
                    } else {
                        $errors[] = "Votre mot de passe doit être différent de l'ancien";
                    }
                } else {
                    $errors[] = "les mots de passe sont pas bon michel !!!!!!";
                }
            } else if ($oldPassword == null && $password == null && $confirmPassword == null) {
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('app_profil_afficher', [
                    'id' => $user->getId()
                ]);
            } else {
                $errors[] = "Pour modifier votre mot de passe, vous devez saisir votre ancien mot de passe, puis le nouveau, puis le confirmer";
            }

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
    public function afficherProfil($id, UserRepository $userRepo): Response
    {
        $user = $userRepo->find($id);

        return $this->render('modification_profil/profil.html.twig', [
            'user' => $user
        ]);
    }
}
