<?php

namespace Bangpound\Bundle\FiniteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FiniteGraphCommand
 * @package Bangpound\Bundle\FiniteBundle\Command
 */
class FiniteGraphCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDocs}
     */
    protected function configure()
    {
        $this
          ->setName('finite:graph')
          ->addArgument('name', InputArgument::REQUIRED, 'State machine name')
        ;
    }

    /**
     * {@inheritDocs}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $stateMachine = $this->getContainer()->get('finite.state_machine');
        if ($this->getContainer()->has('bangpound_finite.loader.'.$name)) {
            $loader = $this->getContainer()->get('bangpound_finite.loader.'.$name);
            $loader->load($stateMachine);
        }

        $graph = $this->getContainer()->get('bangpound_finite.graph');
        $output->write($graph->render($stateMachine));
    }
}
