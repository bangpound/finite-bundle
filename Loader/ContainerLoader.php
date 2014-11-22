<?php

namespace Bangpound\Bundle\FiniteBundle\Loader;

use Finite\Loader\LoaderInterface;
use Finite\State\Accessor\PropertyPathStateAccessor;
use Finite\StateMachine\StateMachineInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerLoader implements LoaderInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge(
            array(
                'class'         => '',
                'graph'         => 'default',
                'property_path' => 'finiteState',
                'states'        => array(),
                'transitions'   => array(),
            ),
            $config
        );
    }

    /**
     * @{inheritDoc}
     * @param StateMachineInterface $stateMachine
     */
    public function load(StateMachineInterface $stateMachine)
    {
        $stateMachine->setStateAccessor(new PropertyPathStateAccessor($this->config['property_path']));
        $stateMachine->setGraph($this->config['graph']);

        array_walk($this->config['states'], array($stateMachine, 'addState'));
        array_walk($this->config['transitions'], array($stateMachine, 'addTransition'));
    }

    /**
     * @inheritdoc
     */
    public function supports($object, $graph = 'default')
    {
        $reflection = new \ReflectionClass($this->config['class']);

        return $reflection->isInstance($object) && $graph === $this->config['graph'];
    }

    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
