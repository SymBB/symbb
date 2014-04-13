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

class SymBBCoreConfigExtension extends Extension implements PrependExtensionInterface
{
    
    public function prepend(ContainerBuilder $container) 
    {
        
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('doctrine.yml');
        $loader->load('twig.yml');
        $loader->load('fos_user.yml');
        $loader->load('fos_rest.yml');
        $loader->load('fos_messages.yml');
        $loader->load('knp.yml');
        $loader->load('lsw_memcache.yml');
        $loader->load('swiftmailer.yml');
        $loader->load('framework.yml');
        $loader->load('vich_uploader.yml');
        $loader->load('jms_translation.yml');
        $loader->load('liip_imagine.yml');
        $loader->load('symbb.yml');

    }
        
    public function load(array $configs, ContainerBuilder $container)
    {        
        
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        $config = array();
        // reverse array
        $configs = array_reverse($configs);
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }
      
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, array($config));
        
        $container->setParameter('symbb_config', $config);
        $container->setParameter('twig.globals.symbb_config', $config);
    }
}
