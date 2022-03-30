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
        $error = null;
        // CREATION FORMULAIRE
        $formModifProfil = $this->createForm(ModificationProfilType::class, $user);
        $formModifProfil->handleRequest($request);

        // traitement du formulaire
        $oldPassword = $formModifProfil->get("oldPassword")->getData();
        $password = $formModifProfil->get("password")->getData();
        $confirmPassword = $formModifProfil->get("confirm_password")->getData();

        if ($formModifProfil->isSubmitted() && $formModifProfil->isValid()) {
            if ($oldPassword != null && $password != null && $confirmPassword != null) {
                $oldPasswordHasher = null;
                $oldPasswordHasher->hashPassword($oldPassword);
                if ($user->getPassword() == $oldPasswordHasher && $password == $confirmPassword) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $password
                        )
                    );
                } else {
                    $error = "les mots de passe sont pas bon michel !!!!!!";
                    return $this->render('modification_profil/modifProfil.html.twig', [
                        'formModifProfil' => $formModifProfil->createView(), "error" => $error,
                    ]);
                }
            } else if ($oldPassword == null && $password == null && $confirmPassword == null) {

            } else {
                $error = "Pour modifier votre mot de passe, vous devez saisir votre ancien mot de passe, puis le nouveau, puis le confirmer";
                return $this->render('modification_profil/modifProfil.html.twig', [
                    'formModifProfil' => $formModifProfil->createView(), "error" => $error,
                ]);
            }
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_profil_afficher', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('modification_profil/modifProfil.html.twig', [
            'formModifProfil' => $formModifProfil->createView(),
            'user' => $user,
            'error' => $error
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
