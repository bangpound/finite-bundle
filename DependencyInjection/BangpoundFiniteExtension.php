<?php

namespace Bangpound\Bundle\FiniteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BangpoundFiniteExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config as $key => $stateMachineConfig) {
            $stateMachineConfig = $this->removeDisabledCallbacks($stateMachineConfig);

            $definition = new DefinitionDecorator('bangpound_finite.container_loader');
            $definition->replaceArgument(0, $stateMachineConfig);
            $definition->addTag('bangpound_finite.loader');

            // setLazy method wasn't available before 2.3, FiniteBundle requirement is ~2.1
            if (method_exists($definition, 'setLazy')) {
                $definition->setLazy(true);
            }

            $serviceId = 'bangpound_finite.loader.'.$key;
            $container->setDefinition($serviceId, $definition);
        }
    }

    /**
     * Remove callback entries where index 'disabled' is set to true.
     *
     * @param array $config
     *
     * @return array
     */
    protected function removeDisabledCallbacks(array $config)
    {
        if (!isset($config['callbacks'])) {
            return $config;
        }

        foreach (array('before', 'after') as $position) {
            foreach ($config['callbacks'][$position] as $i => $callback) {
                if (isset($callback['disabled'])) {
                    unset($config['callbacks'][$position][$i]);
                } else {
                    unset($callback['disabled']);
                }
            }
        }

        return $config;
    }
}
