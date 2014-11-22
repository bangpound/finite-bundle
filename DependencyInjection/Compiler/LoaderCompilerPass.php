<?php

namespace Bangpound\Bundle\FiniteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LoaderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $loaders = $container->findTaggedServiceIds('bangpound_finite.loader');

        $factoryDefinition = $container->getDefinition('finite.factory');

        foreach (array_keys($loaders) as $id) {
            $definition = $container->getDefinition($id);
            $definition->addMethodCall('setContainer', array(new Reference('service_container')));
            $factoryDefinition->addMethodCall('addLoader', array(new Reference($id)));

            $configs = $definition->getArgument(0);
            $configs['states'] = $this->buildArguments($container, $configs['states'], array($this, 'transformStateConfigToArgs'));
            $configs['transitions'] = $this->buildArguments($container, $configs['transitions'], array($this, 'transformTransitionConfigToArgs'));
            $definition->replaceArgument(0, $configs);
        }
    }

    public static function transformStateConfigToArgs($key, $config)
    {
        return array($key, $config['type'], array(), $config['properties']);
    }

    public static function transformTransitionConfigToArgs($key, $config)
    {
        return array($key, $config['from'], $config['to'], $config['guard']);
    }

    /**
     * Applies a transformation to the configuration so they can serve as
     * constructor arguments. The arguments are applied to original definition
     * and then it is cloned for use in the loader's constructor.
     *
     * @param  ContainerBuilder $container
     * @param $configs
     * @param $transformer
     * @return array
     */
    private function buildArguments(ContainerBuilder $container, $configs, $transformer)
    {
        return array_map(function ($config, $key) use ($container, $transformer) {
              $container->getDefinition($config['id'])
                ->setArguments(call_user_func_array($transformer, array($key, $config)));

              return clone $container->getDefinition($config['id']);
          }, $configs, array_keys($configs));
    }
}
