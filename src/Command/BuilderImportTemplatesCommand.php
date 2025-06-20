<?php

namespace VeeZions\BuilderEngine\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

#[AsCommand(
    name: 'builder:import-templates',
    description: 'Import CRUD templates from BuilderEngine.',
)]
class BuilderImportTemplatesCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $params,
        private string $mode,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('BuilderEngineBundle templates list');

        if (ConfigConstant::CONFIG_MODE_FORM === $this->mode) {
            $io->text('You cannot import template in <comment>'.ConfigConstant::CONFIG_MODE_FORM.'</comment> mode.');
            $io->text('You have to switch mode to <info>'.ConfigConstant::CONFIG_MODE_FORM.'</info> in <question>config/packages/'.ConfigConstant::CONFIG_FILE_NAME.'.yaml</question> file.');
            $io->error('Aborting templates import.');

            return Command::SUCCESS;
        }

        $projectDir = $this->params->get('kernel.project_dir');
        if (!is_string($projectDir)) {
            throw new \RuntimeException('Project directory must be a string');
        }

        $templatesPath = $projectDir.'/vendor/veezions/builder-engine-bundle/src/Resources/views';
        $destinationPath = $projectDir.'/templates/bundles/BuilderEngineBundle/';

        $filesystem = new Filesystem();
        if ($filesystem->exists($templatesPath)) {
            $filesystem->mirror($templatesPath, $destinationPath);
        }

        $io->text([
            '<info>Articles</info> admin templates',
            '<info>Categories</info> admin templates',
            '<info>Libraries</info> admin templates',
            '<info>Navigations</info> admin templates',
            '<info>Pages</info> templates',
            '<comment>Base</comment> template',
        ]);

        $io->newLine();
        $io->text('Destination folder: <question>./templates/bundles/BuilderEngineBundle</question>');

        $io->success('All templates have been imported successfully !');

        return Command::SUCCESS;
    }
}
