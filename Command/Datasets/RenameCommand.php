<?php

namespace Kachkaev\PostgresHelperBundle\Command\Datasets;

use Symfony\Component\Console\Input\InputArgument;

use Kachkaev\PostgresHelperBundle\Command\AbstractParameterAwareCommand;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class RenameCommand extends AbstractParameterAwareCommand
{
    
    protected function configure()
    {
        $this
            ->setName('ph:datasets:rename')
            ->setDescription('Renames given dataset')
            ->makeDatasetAware()
            ->addArgument('dataset-new-name', InputArgument::REQUIRED, 'New name of the dataset (without schema)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->processInput($input, $output, $extractedArguments);

        $datasetManager = $this->getDatasetManager($extractedArguments['dataset-schema']);
        $datasetManager->rename($extractedArguments['dataset-name'], $input->getArgument("dataset-new-name"));
    }
}