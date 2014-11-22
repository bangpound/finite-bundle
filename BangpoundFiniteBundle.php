<?php

namespace Bangpound\Bundle\FiniteBundle;

use Bangpound\Bundle\FiniteBundle\DependencyInjection\Compiler\LoaderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BangpoundFiniteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LoaderCompilerPass());
    }
}
