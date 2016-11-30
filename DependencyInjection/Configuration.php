<?php
namespace Dreamlex\Bundle\GoogleSpreadsheetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Dreamlex\Bundle\GoogleSpreadsheetBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dreamlex_google_spreadsheet');

        $rootNode
            ->children()
                ->arrayNode('scope')
                    ->prototype('scalar')
                        ->defaultValue('spreadsheets_readonly')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
                ->scalarNode('app_name')
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
