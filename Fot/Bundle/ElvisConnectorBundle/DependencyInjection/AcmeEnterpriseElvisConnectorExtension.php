<?php

namespace Fot\Bundle\ElvisConnectorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @author Marie Bochu <marie.bochu@akeneo.com>
 */
class FotElvisConnectorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
//        $loader->load('entities.yml');
//        $loader->load('savers.yml');
        $loader->load('services.yml');
        $loader->load('attribute_types.yml');
        $loader->load('providers.yml');
        $loader->load('updaters.yml');
    }
}
