<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class SymBBCoreSystemExtension extends Extension implements PrependExtensionInterface
{

    public function prepend(ContainerBuilder $container)
    {

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('doctrine.yml');

        $prefix = '';
        foreach ($container->getExtensions() as $name => $extension) {
            // web profiler is only activated in the dev env.
            if ('web_profiler' == $name) {
                $prefix = 'dev_';
                break;
            }
        }

        foreach ($container->getExtensions() as $name => $extension) {
            if ($name == 'security' && !empty($prefix)) {
                $config['acl']['tables']['class'] = $prefix . 'acl_classes';
                $config['acl']['tables']['entry'] = $prefix . 'acl_entries';
                $config['acl']['tables']['object_identity'] = $prefix . 'acl_object_identities';
                $config['acl']['tables']['object_identity_ancestors'] = $prefix . 'acl_object_identity_ancestors';
                $config['acl']['tables']['security_identity'] = $prefix . 'acl_security_identities';
                $container->prependExtensionConfig($name, $config);
            }
        }

    }

    public function load(array $configs, ContainerBuilder $container)
    {
        
    }
}
