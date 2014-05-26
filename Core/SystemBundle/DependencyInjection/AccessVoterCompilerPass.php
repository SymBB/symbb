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

class AccessVoterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

        if (!$container->hasDefinition('symbb.core.access.voter.manager')) {
            return;
        }

        $definition = $container->getDefinition(
            'symbb.core.access.voter.manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'security.voter'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addVoter',
                array(new Reference($id))
            );
        }
    }
}