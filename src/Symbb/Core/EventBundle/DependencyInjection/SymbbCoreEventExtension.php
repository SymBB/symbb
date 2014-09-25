<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace Symbb\Core\EventBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class SymbbCoreEventExtension extends Extension implements ExtensionInterface
{
    
    public function load(array $configs, ContainerBuilder $container)
    {        
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

}
