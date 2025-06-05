<?php

namespace VeeZions\BuilderEngine\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'builder:deploy-blog',
    description: 'Deploy Blog Controllers from BuilderEngine.',
)]
class BuilderDeployBlogCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $params,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('BuilderEngineBundle Controllers list');

        $projectDir = $this->params->get('kernel.project_dir');
        if (!is_string($projectDir)) {
            throw new \RuntimeException('Project directory must be a string');
        }

        $stubsPath = $projectDir.'/vendor/veezions/builder-engine-bundle/src/Resources/stubs/';
        $destinationPath = $projectDir.'/src/Controller/BuilderEngineResources/';
        
        $files = [
            ['stub' => 'article.stub', 'destination' => 'ArticleController.php'],
            ['stub' => 'category.stub', 'destination' => 'CategoryController.php'],
            ['stub' => 'blog.stub', 'destination' => 'BlogController.php'],
        ];

        $filesystem = new Filesystem();
        if (!$filesystem->exists($destinationPath)) {
            $filesystem->mkdir($destinationPath);
        }
        
        $results = [];
        foreach ($files as $file) {
            if (!$filesystem->exists($destinationPath.$file['destination'])) {
                $filesystem->copy($stubsPath.$file['stub'], $destinationPath.$file['destination']);
                $results[] = '<info>'.$file['destination'].'</info> has been deployed successfully.';
            } else {
                $results[] = 'File <comment>'.$file['destination'].'</comment> already exists. Skipping...';
            }
        }

        $io->text($results);

        $io->success('All Controllers have been deployed successfully !');

        return Command::SUCCESS;
    }
}
