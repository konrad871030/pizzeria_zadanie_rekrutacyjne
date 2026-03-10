<?php

namespace App\Command;

use App\Service\DbConnectionFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:migrate', description: 'Creates required database tables')]
final class AppMigrateCommand extends Command
{
    public function __construct(private readonly DbConnectionFactory $connectionFactory)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sql = file_get_contents(__DIR__.'/../../migrations/Version20260306160000.sql');
        if ($sql === false) {
            $output->writeln('<error>Cannot read migration SQL file.</error>');

            return Command::FAILURE;
        }

        $this->connectionFactory->getConnection()->exec($sql);
        $output->writeln('<info>Database migration completed.</info>');

        return Command::SUCCESS;
    }
}
