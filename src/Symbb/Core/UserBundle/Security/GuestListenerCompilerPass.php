<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/


namespace Symbb\Core\UserBundle\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


class GuestListenerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('security.authentication.listener.anonymous')) {
            return;
        }

        $definition = $container->getDefinition(
            'security.authentication.listener.anonymous'
        );

        $definition->setClass('Symbb\Core\UserBundle\Security\Firewall\GuestListener');
        $managers = $container->getParameter('doctrine.entity_managers');
        $manager = $managers['symbb'];
        $definition->addArgument(new Reference($manager));
    }
}