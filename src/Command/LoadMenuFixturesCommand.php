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
            ['name' => 'Margherita', 'price_cents' => 2800, 'ingredients' => 'Sos pomidorowy, mozzarella, bazylia', 'image_name' => 'margherita.png'],
            ['name' => 'Capricciosa', 'price_cents' => 3400, 'ingredients' => 'Sos pomidorowy, mozzarella, szynka, pieczarki', 'image_name' => 'capricciosa.png'],
            ['name' => 'Pepperoni', 'price_cents' => 3600, 'ingredients' => 'Sos pomidorowy, mozzarella, pepperoni', 'image_name' => 'pepperoni.png'],
            ['name' => 'Diavola', 'price_cents' => 3700, 'ingredients' => 'Sos pomidorowy, mozzarella, salami piccante, chili', 'image_name' => 'diavola.png'],
            ['name' => 'Funghi', 'price_cents' => 3300, 'ingredients' => 'Sos pomidorowy, mozzarella, pieczarki', 'image_name' => 'funghi.png'],
            ['name' => 'Quattro Formaggi', 'price_cents' => 3900, 'ingredients' => 'Mozzarella, gorgonzola, parmezan, provolone', 'image_name' => 'quattro_formaggi.png'],
            ['name' => 'Hawajska', 'price_cents' => 3500, 'ingredients' => 'Sos pomidorowy, mozzarella, szynka, ananas', 'image_name' => 'hawajska.png'],
            ['name' => 'Wiejska', 'price_cents' => 4100, 'ingredients' => 'Sos pomidorowy, mozzarella, kiełbasa, cebula, ogorek', 'image_name' => 'wiejska.png'],
        ]);

        $output->writeln('<info>Menu fixtures loaded.</info>');

        return Command::SUCCESS;
    }
}
