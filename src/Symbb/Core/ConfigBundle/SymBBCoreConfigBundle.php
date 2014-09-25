<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\ConfigBundle;

use SymBB\Core\ConfigBundle\DependencyInjection\SymBBCoreConfigExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SymBBCoreConfigBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        // register extensions that do not follow the conventions manually
        $container->registerExtension(new SymBBCoreConfigExtension());
    }
}
