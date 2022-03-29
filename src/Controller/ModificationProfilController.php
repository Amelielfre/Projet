<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModificationProfilType;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profil", name="app_profil_")
 */
class ModificationProfilController extends AbstractController
{
    /**
     * @Route("/modifier", name="modifier")
     */
    public function modifProfil(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $user = $this->getUser();
        // CREATION FORMULAIRE
        $formModifProfil = $this->createForm(ModificationProfilType::class, $user);
        $formModifProfil->handleRequest($request);

        if ($formModifProfil->isSubmitted() && $formModifProfil->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_profil_afficher', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('modification_profil/modifProfil.html.twig', [
            'formModifProfil' => $formModifProfil->createView(),
            'user' => $user,
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
