<?php

namespace App\Command;

use App\Repository\MenuItemRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fixtures:menu', description: 'Loads demo menu items')]
final class LoadMenuFixturesCommand extends Command
{
    public function __construct(private readonly MenuItemRepository $menuItemRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->menuItemRepository->replaceAll([
            ['name' => 'Margherita', 'price_cents' => 2800],
            ['name' => 'Capricciosa', 'price_cents' => 3400],
            ['name' => 'Pepperoni', 'price_cents' => 3600],
            ['name' => 'Diavola', 'price_cents' => 3700],
            ['name' => 'Funghi', 'price_cents' => 3300],
            ['name' => 'Quattro Formaggi', 'price_cents' => 3900],
            ['name' => 'Hawajska', 'price_cents' => 3500],
            ['name' => 'Wiejska', 'price_cents' => 4100],
        ]);

        $output->writeln('<info>Menu fixtures loaded.</info>');

        return Command::SUCCESS;
    }
}
