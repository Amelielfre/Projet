<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
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
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $error[] = null;
            dump($error);
            $user = new User();
            $user->setActif(true);
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if($this->userRepo->findBy(["email"=>$user->getEmail()])){
                    $error["email"] = "Email déjà existant";
            }

            if ($this->userRepo->findBy(['pseudo' => $user->getPseudo()])) {
                $error["pseudo"] = "Pseudo déjà existant";
            }
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
                        dump($error);
                        if(count($error) < 2){

                            $entityManager->persist($user);
                            $entityManager->flush();
                            return $this->redirectToRoute("app_login");
                        }
                    } else {
                        $error["mdp"] = "Les mots de passe sont pas bon michel !!!!!!";
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
