<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModificationProfilType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModificationProfilController extends AbstractController
{
    /**
     * @Route("/modification/profil", name="app_modification_profil")
     */
    public function index(Request $request): Response
    {

        // CREATION OBJET
        $user = new User();

        // CREATION FORMULAIRE
        $formModifProfil = $this->createForm(ModificationProfilType::class, $user);

        // AJOUT DONNEES
        $formModifProfil->handleRequest($request);



        return $this->render('modification_profil/modifProfil.html.twig', [
            'formModifProfil' => $formModifProfil->createView(),
            'user' => $user,
        ]);
    }
}
