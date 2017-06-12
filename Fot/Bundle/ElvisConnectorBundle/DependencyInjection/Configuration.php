<?php

    namespace Fot\Bundle\ElvisConnectorBundle\DependencyInjection;
    use Symfony\Component\Config\Definition\Builder\TreeBuilder;
    use Symfony\Component\Config\Definition\ConfigurationInterface;


    class Configuration implements ConfigurationInterface
    {

        /**
         * {@inheritDoc}
         */
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();

            $root = $treeBuilder->root('pim_fot_bundle_elvisconnector');

            $children = $root->children();

            $children->arrayNode('settings')
                     ->children()
                        ->arrayNode('base_url')
                            ->children()
                                ->scalarNode('value')->end()
                                ->scalarNode('scope')->end()
                            ->end()
                        ->end()
                        ->arrayNode('username')
                            ->children()
                                ->scalarNode('value')->end()
                                ->scalarNode('scope')->end()
                            ->end()
                        ->end()
                        ->arrayNode('pwd')
                            ->children()
                                ->scalarNode('value')->end()
                                ->scalarNode('scope')->end()
                            ->end()
                            ->end()
                        ->end()
                    ->end();

            $children->end();

            $root->end();

            return $treeBuilder;
        }
    }
