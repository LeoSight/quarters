<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactionController extends AbstractController
{
    #[Route('/game/faction', name: 'game_faction')]
    public function index(): Response
    {
        return $this->render('game/faction.twig');
    }
}