<?php

namespace XenoLab\XenoEngine\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'xeno:libraries-references',
    description: 'List all external JS libraries and their versions used by XenoEngine.',
)]
class XenoLibrariesReferencesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('XenoEngineBundle external libraries references list');



        return Command::SUCCESS;
    }
}
