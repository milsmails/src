<?php

namespace Fot\Bundle\ElvisConnectorBundle;
use Fot\Bundle\ElvisConnectorBundle\DependencyInjection\Compiler\OroConfigCompilerPass;
use Fot\Bundle\ElvisConnectorBundle\DependencyInjection\FotElvisConnectorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FotElvisConnectorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new OroConfigCompilerPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new FotElvisConnectorExtension();
        }

        return $this->extension;
    }
}