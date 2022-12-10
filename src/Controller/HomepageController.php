<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function guest(): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('game_dashboard');
        }

        return $this->render('homepage.twig');
    }
}