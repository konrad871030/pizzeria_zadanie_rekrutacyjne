<?php

namespace App\Command;

use App\Repository\OrderRepository;
use App\Service\AppLogger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:process-next-order', description: 'Processes one pending order and logs queue overload warnings')]
final class ProcessNextOrderCommand extends Command
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly AppLogger $logger,
        private readonly int $queueAlertThreshold,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pendingCount = $this->orderRepository->countPending();
        if ($pendingCount >= $this->queueAlertThreshold) {
            $message = sprintf('Long queue detected: %d pending orders.', $pendingCount);
            $this->logger->warning($message);
            $output->writeln(sprintf('<comment>%s</comment>', $message));
        }

        $orderId = $this->orderRepository->findOldestPendingId();
        if ($orderId === null) {
            $output->writeln('<info>No pending orders to process.</info>');

            return Command::SUCCESS;
        }

        $this->orderRepository->markDelivered($orderId);
        $this->logger->info(sprintf('Order %d marked as delivered.', $orderId));
        $output->writeln(sprintf('<info>Order %d processed.</info>', $orderId));

        return Command::SUCCESS;
    }
}
