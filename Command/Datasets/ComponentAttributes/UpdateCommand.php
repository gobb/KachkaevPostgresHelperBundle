<?php

namespace Kachkaev\PostgresHelperBundle\Command\Datasets\ComponentAttributes;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kachkaev\PostgresHelperBundle\Command\AbstractParameterAwareCommand;

class UpdateCommand extends AbstractParameterAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ph:datasets:component-attributes:update')
            ->setDescription('Updates given attributes of the given dataset component')
            ->makeDatasetAware()
            ->addArgument('component-name', InputArgument::REQUIRED, 'Name of the component')
            ->addArgument('attribute-names', InputArgument::REQUIRED, 'Comma-separated names of the attributes to update')
            ->addOption('ids', null, InputOption::VALUE_REQUIRED,
                    'ids of records to update attributes for',
                    null)
            ->addOption('filter', null, InputOption::VALUE_REQUIRED,
                    'sql WHERE to filter records and get their ids',
                    null)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->processInput($input, $output, $extractedArguments);
        
        $datasetManager = $this->getDatasetManager($extractedArguments['dataset-schema']);
        $dataset = $datasetManager->get($extractedArguments['dataset-name']);
        
        $componentName = $input->getArgument('component-name');
        $attributeNames = explode(',', $input->getArgument('attribute-names'));
        $attributeManager = $dataset->getComponentAttributeManager();
        
        // Extract ids
        if ($input->getOption('ids')) {
            // From an argument
            $ids = explode(',',$input->getOption('ids'));
        } else if ($input->getOption('filter')) {
            // From the filter
            $ids = $attributeManager->getIdsWhere('items', $input->getOption('filter'));
        } else {
            throw new \InvalidArgumentException("Either option ids or filter must be defined.");
        }
        
        if (!count($ids)) {
            $output->writeln('<error>No records to process</error>');
            return;
        }
        
        $output->writeln(sprintf('Updating attributes for %s records...', count($ids)));
        $progress = $this->getHelper('progress');
        $progress->start($output, count($ids));
        
        // Update records by chunks
        $chunkSize = $this->getContainer()->getParameter('postgres_helper.batch_chunk_size');
        $idChunks = array_chunk($ids, $chunkSize);
        
        foreach ($idChunks as $idChunk) {
            $attributeManager->updateAttributes($componentName, $attributeNames, $idChunk);
            $progress->advance(count($idChunk));
        }
        $progress->finish();
        
        $output->writeln(' Done.');
    }
}