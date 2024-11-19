<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ItemImporterService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'import:item:api',
    description: 'The command to import items from API',
)]
class ImportItemApiCommand extends Command
{
    public function __construct(
        private readonly ItemImporterService $importer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Starting import of items from API');

        try {
            $count = $this->importer->import();
            $io->success(sprintf('Imported %d items successfully.', $count));
        } catch (\Throwable $e) {
            $io->error('Import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
