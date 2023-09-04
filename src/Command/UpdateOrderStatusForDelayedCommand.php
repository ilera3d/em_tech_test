<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-order-status-for-delayed',
    description: 'Updates status to delayed on any order where current date/time is after expected delivery',
)]
class UpdateOrderStatusForDelayedCommand extends Command
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    { }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $conn = $this->entityManager->getConnection();

        // Note this uses sqlite syntax, would need to swap to now() for mysql etc

        $sql = "UPDATE customer_order set status = 'delayed' where estimated_delivery_date_time < date('now') and status = 'processing'";
        $statement = $conn->prepare($sql);
        $result = $statement->executeQuery();

        $output->writeln($result->rowCount() . ' rows updated');

        return Command::SUCCESS;
    }
}
