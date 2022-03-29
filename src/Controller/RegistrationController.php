<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(SiteRepository $siteRepo, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $error = null;
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        //Récupération des mot de passe
        $password = $form->get("password")->getData();
        $confirmPassword = $form->get("confirm_password")->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            //Check des mots de passe
            if ($password == $confirmPassword) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();
               return $this->redirectToRoute("app_login");
            } else {
                $error = "les mots de passe sont pas bon michel !!!!!!";
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(), "error" => $error,
                ]);

            }
            // do anything else you need here, like send an email

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(), "error" => $error,
        ]);
    }
}
