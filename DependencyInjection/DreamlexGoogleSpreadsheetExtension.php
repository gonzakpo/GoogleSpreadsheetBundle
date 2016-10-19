<?php
namespace Dreamlex\Bundle\GoogleSpreadsheetBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class DreamlexGoogleSpreadsheetExtension
 *
 * @package Dreamlex\Bundle\GoogleSpreadsheetBundle\DependencyInjection
 */
class DreamlexGoogleSpreadsheetExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $container->setParameter('dreamlex_google_spreadsheet.scope', $config['scope']);
        $container->setParameter('dreamlex_google_spreadsheet.app_name', $config['app_name']);
    }
}
