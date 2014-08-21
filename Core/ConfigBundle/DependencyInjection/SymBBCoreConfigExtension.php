<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\ConfigBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Parser;

class SymBBCoreConfigExtension extends Extension implements PrependExtensionInterface
{
    
    public function prepend(ContainerBuilder $container) 
    {

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('doctrine.yml');
        $loader->load('twig.yml');
        $loader->load('fos_user.yml');
        $loader->load('fos_rest.yml');
        $loader->load('knp.yml');
        $loader->load('swiftmailer.yml');
        $loader->load('framework.yml');
        $loader->load('vich_uploader.yml');
        $loader->load('jms_translation.yml');
        $loader->load('liip_imagine.yml');
        $loader->load('fosjsrouting.yml');
        $loader->load('assetic.yml');
    }
        
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__.'/../Resources/config');
        $yaml = new Parser();
        $myConfig = $yaml->parse(file_get_contents(($locator->locate('symbb.yml'))));

        $loader = new YamlFileLoader($container, $locator);
        $loader->load('services.yml');
        $config = array();
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }

        $config = array_merge_recursive($config, $myConfig);

        $configuration = new \SymBB\Core\ConfigBundle\DependencyInjection\Configuration();
        $config        = $this->processConfiguration($configuration, array($config));
 
        $container->setParameter('symbb_config', $config);
        $container->setParameter('twig.globals.symbb_config', $config);
    }
}
