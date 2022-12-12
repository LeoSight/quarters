<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/game', name: 'game_dashboard')]
    public function index(): Response
    {
        return $this->render('game/dashboard.twig');
    }
}