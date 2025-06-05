<?php

namespace VeeZions\BuilderEngine\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'builder:remove-resources',
    description: 'Remove all resources deployed by BuilderEngine installation.',
)]
class BuilderRemoveResourcesCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $params,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_OPTIONAL,
            'Force resources removal without asking for confirmation.',
            false
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');
        
        $io->title('BuilderEngineBundle Controllers list');

        if (false === $force) {
            $choice = $io->choice(
                'Are you sure you want to remove all BuilderEngine resources ?',
                ['Yes', 'No'],
                'Yes'
            );

            if ('No' === $choice) {
                $io->warning('Aborting resources removal.');
                return Command::SUCCESS;
            }
        } else {
            $io->warning('You are running this command with --force option. All BuilderEngine resources will be removed.');
        }

        $projectDir = $this->params->get('kernel.project_dir');
        if (!is_string($projectDir)) {
            throw new \RuntimeException('Project directory must be a string');
        }

        $controllersPath = $projectDir.'/src/Controller/BuilderEngineResources/';
        $templatesPath = $projectDir.'/templates/bundles/BuilderEngineBundle/';

        $filesystem = new Filesystem();
        if ($filesystem->exists($controllersPath)) {
            $filesystem->remove($controllersPath);
        }

        if ($filesystem->exists($templatesPath)) {
            $filesystem->remove($templatesPath);
        }

        $io->success('All resources have been removed successfully !');

        return Command::SUCCESS;
    }
}
