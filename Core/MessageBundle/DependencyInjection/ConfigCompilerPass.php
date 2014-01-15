<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/


namespace SymBB\Core\MessageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


class ConfigCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('fos_message.message_manager.default')) {
            $definition = $container->getDefinition(
                'fos_message.message_manager.default'
            );

            $managers   = $container->getParameter('doctrine.entity_managers');
            $manager    = $managers['symbb'];
            $doctrineRef = new Reference($manager);

            $definition->replaceArgument(0, $doctrineRef);
        }

        if ($container->hasDefinition('fos_message.thread_manager.default')) {
            $definition = $container->getDefinition(
                'fos_message.thread_manager.default'
            );

            $managers   = $container->getParameter('doctrine.entity_managers');
            $manager    = $managers['symbb'];
            $doctrineRef = new Reference($manager);

            $definition->replaceArgument(0, $doctrineRef);
        }
        
    }
}