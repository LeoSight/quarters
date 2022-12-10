<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/game/map', name: 'game_map')]
    public function index(): Response
    {
        return $this->render('game/map.twig');
    }
}
