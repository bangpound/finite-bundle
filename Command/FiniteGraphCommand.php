<?php

namespace Bangpound\Bundle\FiniteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FiniteGraphCommand.
 */
class FiniteGraphCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
          ->setName('finite:graph')
          ->addArgument('name', InputArgument::REQUIRED, 'State machine name')
        ;
    }

    /**
     * {@inheritdoc}
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
