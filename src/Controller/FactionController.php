<?php

namespace App\Controller;

use App\Entity\Faction;
use App\Entity\User;
use App\Repository\FactionRepository;
use App\Repository\UserRepository;
use App\Service\BattleService;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactionController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly FactionRepository $factionRepository,
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
        private readonly BattleService $battleService
    ) {}

    #[Route('/game/faction', name: 'game_faction')]
    public function index(): Response
    {
        $user = $this->userService->entity($this->getUser());

        $myFaction = $user->getFaction();
        $twigArgs = [ 'me' => $user, 'faction' => $myFaction ];

        if($myFaction){
            $twigArgs['applicants'] = $myFaction->getApplicants();
        }else{
            $twigArgs['soldiers'] = count($user->getSoldiers());
            $twigArgs['applications'] = $user->getApplications()->map(function($obj){return $obj->getId();})->getValues();
            $twigArgs['factions'] = $this->factionRepository->findAll();
        }

        return $this->render('game/faction.twig', $twigArgs);
    }

    #[Route('/game/faction/join/{id}', name: 'game_faction_join', requirements: ['id' => '\d+'])]
    public function join(int $id): Response
    {
        $user = $this->userService->entity($this->getUser());

        $faction = $this->factionRepository->find($id);
        if (!$faction) {
            throw new \RuntimeException("Faction not found!");
        }

        $user->addApplication($faction);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_faction');
    }

    #[Route('/game/faction/accept/{id}', name: 'game_faction_accept', requirements: ['id' => '\d+'])]
    public function accept(int $id): Response
    {
        $user = $this->userService->entity($this->getUser());

        $faction = $user->getFaction();
        if (!$faction || $faction->getLeader() !== $user) {
            throw new \RuntimeException("User doesn't lead a faction!");
        }

        $target = $this->userRepository->find($id);
        if (!$target) {
            throw new \RuntimeException("That user doesn't exist!");
        }

        if (!$faction->getApplicants()->contains($target)) {
            throw new \RuntimeException("This user hasn't applied!");
        }

        $target->removeAllApplications();
        $faction->addUser($target);

        $this->doctrine->getManager()->persist($faction);
        $this->doctrine->getManager()->persist($target);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_faction');
    }

    #[Route('/game/faction/deny/{id}', name: 'game_faction_deny', requirements: ['id' => '\d+'])]
    public function deny(int $id): Response
    {
        $user = $this->userService->entity($this->getUser());

        $faction = $user->getFaction();
        if (!$faction || $faction->getLeader() !== $user) {
            throw new \RuntimeException("User doesn't lead a faction!");
        }

        $target = $this->userRepository->find($id);
        if (!$target) {
            throw new \RuntimeException("That user doesn't exist!");
        }

        if (!$faction->getApplicants()->contains($target)) {
            throw new \RuntimeException("This user hasn't applied!");
        }

        $target->removeApplication($faction);

        $this->doctrine->getManager()->persist($faction);
        $this->doctrine->getManager()->persist($target);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_faction');
    }

    #[Route('/game/faction/kick/{id}', name: 'game_faction_kick', requirements: ['id' => '\d+'])]
    public function kick(int $id): Response
    {
        $user = $this->userService->entity($this->getUser());

        $faction = $user->getFaction();
        if (!$faction || $faction->getLeader() !== $user) {
            throw new \RuntimeException("User doesn't lead a faction!");
        }

        $target = $this->userRepository->find($id);
        if (!$target) {
            throw new \RuntimeException("That user doesn't exist!");
        }

        if ($target->getFaction() !== $faction) {
            throw new \RuntimeException("This user isn't member of your faction!");
        }

        $faction->removeUser($target);

        $this->doctrine->getManager()->persist($faction);
        $this->doctrine->getManager()->persist($target);
        $this->doctrine->getManager()->flush();

        $this->battleService->startBattles();

        return $this->redirectToRoute('game_faction');
    }

    #[Route('/game/faction/leave', name: 'game_faction_leave')]
    public function leave(): Response
    {
        $user = $this->userService->entity($this->getUser());

        $faction = $user->getFaction();
        if (!$faction || $faction->getLeader() === $user) {
            throw new \RuntimeException("User isn't member of a faction!");
        }

        $faction->removeUser($user);

        $this->doctrine->getManager()->persist($faction);
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $this->battleService->startBattles();

        return $this->redirectToRoute('game_faction');
    }

    #[Route('/game/faction/create', name: 'game_faction_create')]
    public function create(Request $request): Response
    {
        $user = $this->userService->entity($this->getUser());

        if (count($user->getSoldiers()) < 10) {
            throw new \RuntimeException("Not enough soldiers!");
        }

        $title = htmlspecialchars((string)$request->request->get('title'));
        $color = htmlspecialchars(str_replace('#', '', (string)$request->request->get('color')));

        if (strlen($title) < 2 || strlen($title) > 30) {
            throw new \RuntimeException("Faction title is too short or too long!");
        }

        if (strlen($color) != 6) {
            throw new \RuntimeException("Color is not in correct format!");
        }

        $faction = new Faction();
        $faction->setTitle($title);
        $faction->setColor($color);
        $faction->setLeader($user);

        $user->setFaction($faction);
        $user->removeAllApplications();

        $this->doctrine->getManager()->persist($faction);
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_faction');
    }
}