<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/accueil", name="app_accueil")
     */
    public function main(): Response
    {
        dump($this->getUser());
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('main/accueil.html.twig');
    }
}
