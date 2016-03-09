<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\ConfigBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Parser;

class SymbbCoreConfigExtension extends Extension implements PrependExtensionInterface
{

    public function prepend(ContainerBuilder $container)
    {

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        if(!$container->getParameter('symbb_config_disable_fos_user')){
            $loader->load('fos_user.yml');
        }

        if(!$container->getParameter('symbb_config_disable_doctrine')) {
            $loader->load('doctrine.yml');
        }

        if(!$container->getParameter('symbb_config_disable_twig')) {
            $loader->load('twig.yml');
        }

        if(!$container->getParameter('symbb_config_disable_monolog')) {
            $loader->load('monolog.yml');
        }

        if(!$container->getParameter('symbb_config_disable_assetic')) {
            $loader->load('assetic.yml');
        }

        if(!$container->getParameter('symbb_config_disable_fos_rest')) {
            $loader->load('fos_rest.yml');
        }

        if(!$container->getParameter('symbb_config_disable_knp')) {
            $loader->load('knp.yml');
        }

        if(!$container->getParameter('symbb_config_disable_lsw_memcache')) {
            $loader->load('lsw_memcache.yml');
        }

        if(!$container->getParameter('symbb_config_disable_swiftmailer')) {
            $loader->load('swiftmailer.yml');
        }

        if(!$container->getParameter('symbb_config_disable_framework')) {
            $loader->load('framework.yml');
        }

        if(!$container->getParameter('symbb_config_disable_jms_translation')) {
            $loader->load('jms_translation.yml');
        }

        if(!$container->getParameter('symbb_config_disable_liip_imagine')) {
            $loader->load('liip_imagine.yml');
        }

        if(!$container->getParameter('symbb_config_disable_fosjsrouting')) {
            $loader->load('fosjsrouting.yml');
        }

    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $yaml = new Parser();
        $myConfig = $yaml->parse(file_get_contents(($locator->locate('symbb.yml'))));

        $loader = new YamlFileLoader($container, $locator);
        $loader->load('services.yml');
        $config = array();
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }

        $config = array_merge_recursive($config, $myConfig);

        $configuration = new \Symbb\Core\ConfigBundle\DependencyInjection\Configuration();
        $config = $this->processConfiguration($configuration, array($config));

        $container->setParameter('symbb_config', $config);
        $container->setParameter('twig.globals.symbb_config', $config);
    }
}
