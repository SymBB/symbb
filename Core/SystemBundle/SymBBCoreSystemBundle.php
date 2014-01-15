<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\SystemBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SymBBCoreSystemBundle extends Bundle
{
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new \SymBB\Core\SystemBundle\DependencyInjection\AccessManagerCompilerPass());
    }
}
