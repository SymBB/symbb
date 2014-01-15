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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AccessManagerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(
            'symbb.core.access.manager'
        );
        
        $taggedServices = $container->findTaggedServiceIds(
            'symbb.acl.manager'
        );
        
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addAclManager',
                array(new Reference($id))
            );
        }
    }
}