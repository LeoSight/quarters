<?php

namespace App\Controller;

use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/game', name: 'game_dashboard')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $me = new UserService($this->getUser(), $doctrine);

        return $this->render('game/dashboard.twig');
    }
}