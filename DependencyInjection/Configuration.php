<?php

namespace Bangpound\Bundle\FiniteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bangpound_finite');
        $rootProto = $rootNode->useAttributeAsKey('name')->prototype('array')->children();

        $rootProto
            ->scalarNode('class')->isRequired()->end()
            ->scalarNode('graph')->defaultValue('default')->end()
            ->scalarNode('property_path')->defaultValue('finiteState')->end();

        $this->addStateSection($rootProto);
        $this->addTransitionSection($rootProto);
        $this->addCallbackSection($rootProto);
        $rootProto->end()->end();

        return $treeBuilder;
    }

    /**
     * @param NodeBuilder $rootProto
     */
    protected function addStateSection(NodeBuilder $rootProto)
    {
        $rootProto
            ->arrayNode('states')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->enumNode('type')
                            ->defaultValue('normal')
                            ->values(array('initial', 'normal', 'final'))
                        ->end()
                        ->arrayNode('properties')
                            ->useAttributeAsKey('name')
                            ->defaultValue(array())
                            ->prototype('variable')->end()
                        ->end()
                        ->scalarNode('id')->defaultValue('bangpound_finite.state')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param NodeBuilder $rootProto
     */
    protected function addTransitionSection(NodeBuilder $rootProto)
    {
        $rootProto
            ->arrayNode('transitions')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->arrayNode('from')
                            ->prototype('variable')->isRequired()->end()
                        ->end()
                        ->scalarNode('to')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('guard')->defaultValue(null)->end()
                        ->scalarNode('id')->defaultValue('bangpound_finite.transition')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param NodeBuilder $rootProto
     */
    protected function addCallbackSection(NodeBuilder $rootProto)
    {
        $callbacks = $rootProto->arrayNode('callbacks')->children();
        $this->addSubCallbackSection($callbacks, 'before');
        $this->addSubCallbackSection($callbacks, 'after');
        $callbacks->end()->end();
    }

    /**
     * @param NodeBuilder $callbacks
     * @param string      $type
     */
    private function addSubCallbackSection(NodeBuilder $callbacks, $type)
    {
        $callbacks
            ->arrayNode($type)
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('on')->end()
                        ->variableNode('do')->end()
                        ->variableNode('from')->end()
                        ->variableNode('to')->end()
                        ->scalarNode('disabled')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end();
    }
}
