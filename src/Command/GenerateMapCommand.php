<?php

namespace App\Command;

use App\Entity\Town;
use App\Repository\TownRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:generate-map')]
class GenerateMapCommand extends Command
{
    private ObjectManager $manager;

    public function __construct(
        private readonly TownRepository $townRepository,
        private readonly ManagerRegistry $doctrine
    ) {
        parent::__construct();
        $this->manager = $this->doctrine->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $oldTowns = new ArrayCollection($this->townRepository->findAll());
        $newTowns = [
            [ 'Satbury', -5, 3 ],
            [ 'Walden', 6, 2 ],
            [ 'Balerno', -2, -3 ],
            [ 'Carran', 3, 8 ],
            [ 'Stawford', -11, -5 ],
        ];

        foreach($newTowns as $newTown) {
            if (count($oldTowns->filter(function (Town $oldTown) use ($newTown) {
                    return $oldTown->getTitle() == $newTown[0];
                })) == 0) {
                $town = new Town();
                $town->setTitle($newTown[0]);
                $town->setX($newTown[1]);
                $town->setY($newTown[2]);
                $this->manager->persist($town);
                $output->writeln('Town created: ' . $newTown[0]);
            }
        }

        $this->manager->flush();

        return Command::SUCCESS;
    }
}