<?php

namespace Fot\Bundle\ElvisConnectorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Fot\Bundle\ElvisConnectorBundle\DependencyInjection\Configuration as BConfig;
/**
 * This is the class that loads and manages your bundle configuration
 *
 * @author Mils Mails <milsmails@gmail.com>
 */
class FotElvisConnectorExtension extends Extension implements PrependExtensionInterface
{

    public function prepend(ContainerBuilder $container)
    {
      $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('config.yml');

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->prependExtensionConfig($this->getAlias(), $config);
    }

    public function getAlias()
    {
        return 'pim_fot_bundle_elvisconnector';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');
        $loader->load('attribute_types.yml');
        $loader->load('providers.yml');
        $loader->load('denormalizers.yml');
        $loader->load('comparators.yml');
        $loader->load('savers.yml');
        $loader->load('updaters.yml');
//        $loader->load('factories.yml');
//        $loader->load('models.yml');
    }


    protected function loadAttributeIcons(LoaderInterface $loader, ContainerBuilder $container)
    {
        $loader->load('attribute_icons.yml');
        $icons = $container->getParameter('pim_enrich.attribute_icons');
        $icons += $container->getParameter('fot.elvis.attribute_icons');
        $container->setParameter('pim_enrich.attribute_icons', $icons);
    }

}
