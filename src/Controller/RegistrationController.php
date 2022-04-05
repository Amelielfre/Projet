<?php

namespace App\Controller;

use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(UserRepository $userRepo, SiteRepository $siteRepo, Request $request, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $error[] = null;
            $user = new User();
            $user->setActif(true);
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($userRepo->findBy(['pseudo' => $user->getPseudo()])) {
                $error["pseudo"] = "Pseudo déjà existant";
            } else {
                //Récupération des mot de passe
                $password = $form->get("password")->getData();
                $confirmPassword = $form->get("confirm_password")->getData();
                if ($form->isSubmitted() && $form->isValid()) {
                    //Check des mots de passe
                    if ($password == $confirmPassword) {
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $form->get('password')->getData()
                            )
                        );

                        $photo = $form->get('photo')->getData();

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


                        $entityManager->persist($user);
                        $entityManager->flush();
                        return $this->redirectToRoute("app_login");
                    } else {
                        $error["mdp"] = "Les mots de passe sont pas bon michel !!!!!!";
                    }
                }
            }
            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
                "error" => $error,
            ]);
        } else {
            return $this->redirectToRoute("app_accueil");
        }
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() réduit la similarité des noms de fichiers générés
        // uniqid(), qui sont basé sur des timestamps
        return md5(uniqid());
    }
}
