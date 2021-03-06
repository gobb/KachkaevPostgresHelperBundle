<?php

namespace Kachkaev\PostgresHelperBundle\Command\Datasets\Components;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kachkaev\PostgresHelperBundle\Command\AbstractParameterAwareCommand;

class InitCommand extends AbstractParameterAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ph:datasets:components:init')
            ->setDescription('Initialises dataset component (creates a table)')
            ->makeDatasetAware()
            ->addArgument('component-name', InputArgument::REQUIRED, 'Name of the component to initialise')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->processInput($input, $output, $extractedArguments);
        
        $componentName = $input->getArgument('component-name');
        $datasetManager = $this->getDatasetManager($extractedArguments['dataset-schema']);
        $dataset = $datasetManager->get($extractedArguments['dataset-name']);

        $output->write(sprintf('Initialising component <info>%s</info> in dataset <info>%s</info>...', $componentName, $dataset->getFullName()));
        
        $componentManager = $dataset->getComponentManager();
        $componentManager->init($componentName);
        
        $output->writeln(' Done.');
    }
}