<?php

namespace App\Service;

use App\Entity\Battle;
use App\Entity\Notification;
use App\Enum\NotificationTypes;
use App\Repository\ActionRepository;
use App\Repository\BattleRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class BattleService {

    private ObjectManager $manager;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly BattleRepository $battleRepository,
        private readonly ActionRepository $actionRepository,
        private readonly InjuryService $injuryService,
        private readonly ManagerRegistry $doctrine,
    ) {
        $this->manager = $this->doctrine->getManager();
    }

    public function startBattles(): void
    {
        $activeBattles = [];
        $fields = $this->userRepository->findUsersOnSameField();
        foreach($fields as $field) {
            $users = $this->userRepository->findBy(['x' => $field['x'], 'y' => $field['y']]);
            $battle = $this->battleRepository->findOneBy(['x' => $field['x'], 'y' => $field['y']]);
            if (!$battle) {
                $factions = [];
                foreach($users as $user){
                    $factions[] = $user->getFaction() ? $user->getFaction()->getId() : 0;
                }

                // TODO: po přidání diplomacie kontrolovat vztah frakcí
                $factions = array_unique($factions);
                if(count($factions) > 1) {
                    $battle = new Battle();
                    $battle->setX($field['x']);
                    $battle->setY($field['y']);
                    $battle->setLastSimulation(new \DateTime());
                    $this->manager->persist($battle);

                    foreach($users as $user){
                        $notification = new Notification();
                        $notification->setType(NotificationTypes::ALERT);
                        $notification->setMessage('Nástřel! Začala bitva na [' . $field['x'] . ',' . $field['y'] . '] ⚔');
                        $user->addNotification($notification);
                        $this->manager->persist($notification);
                    }
                }
            }

            if ($battle) {
                $activeBattles[] = $battle->getId();
            }
        }

        $battles = $this->battleRepository->findAll();
        foreach($battles as $battle){
            if(!in_array($battle->getId(), $activeBattles)){
                $this->manager->remove($battle);
            }
        }

        $this->manager->flush();
    }

    // pouze basic simulace, později se kompletně překope
    // TODO: zrušení bitvy, pokud nikdo nemá přítomného nepřítele (přestože je více hráčů na jednom poli)
    public function processBattles(): void
    {
        $battles = $this->battleRepository->findAll();
        foreach($battles as $battle){
            $users = $this->userRepository->findBy(['x' => $battle->getX(), 'y' => $battle->getY()]);

            $diff = $battle->getLastSimulation()->diff(new \DateTime());
            $minutes = $diff->days * 24 * 60 + $diff->h * 60 + $diff->i;

            if($minutes > 0){
                $cycles = min(ceil($minutes / 2), 25);

                for($i = 0; $i < $cycles; $i++) {
                    foreach ($users as $user) {
                        $manpower = count($user->getSoldiers());
                        $enemyManpower = 0;
                        $enemies = [];

                        foreach ($users as $enemy) {
                            if ($enemy->getFaction() !== $user->getFaction()) {
                                $enemyManpower += count($enemy->getSoldiers());
                                $enemies[] = $enemy;
                            }
                        }

                        if ($manpower == 0 || $enemyManpower == 0) {
                            continue;
                        }

                        $currentAction = $this->actionRepository->findUserCurrentAction($user);
                        if($currentAction !== null){
                            $enemyManpower *= 2;
                        }

                        $hits = min($manpower, rand(1, (int)ceil($enemyManpower / 2)));
                        for ($h = 0; $h < $hits; $h++) {
                            $soldiersArray = $user->getSoldiers()->toArray();
                            $unfortunate = $soldiersArray[array_rand($soldiersArray)];
                            $unfortunate->setHealth($unfortunate->getHealth() - rand(15, 70));
                            if ($unfortunate->getHealth() <= 0) {
                                $this->manager->remove($unfortunate);

                                // prozatím random
                                $randomEnemy = $enemies[array_rand($enemies)];
                                $randomEnemy->setKills($randomEnemy->getKills() + 1);
                                $user->setDeaths($user->getDeaths() + 1);

                                $bodyParts = $this->injuryService->getBodyParts();
                                $injury = $this->injuryService->getInjuryDescription('SHOT_' . $bodyParts[array_rand($bodyParts)] . '_7RU');

                                $notification = new Notification();
                                $notification->setType(NotificationTypes::ALERT);
                                $notification->setMessage('Ale né! Přišli jsme o vojáka! ' . $unfortunate->getName() . ' 💀 ' . $injury);
                                $user->addNotification($notification);
                                $this->manager->persist($notification);
                            }
                        }
                    }
                }

                $battle->setLastSimulation(new \DateTime());
            }
        }

        $this->manager->flush();

        $this->endBattles();
    }

    public function endBattles(): void
    {
        $battles = $this->battleRepository->findAll();
        foreach($battles as $battle) {
            $users = $this->userRepository->findBy(['x' => $battle->getX(), 'y' => $battle->getY()]);
            $factions = [];
            foreach ($users as $user) {
                $factions[] = $user->getFaction() ? $user->getFaction()->getId() : 0;
            }

            // TODO: po přidání diplomacie kontrolovat vztah frakcí
            $factions = array_unique($factions);
            if (count($factions) <= 1) {
                $this->manager->remove($battle);
            }
        }

        $this->manager->flush();
    }

}