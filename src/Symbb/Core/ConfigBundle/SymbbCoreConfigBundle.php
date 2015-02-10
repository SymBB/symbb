<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\ConfigBundle;

use Symbb\Core\ConfigBundle\DependencyInjection\SymbbCoreConfigExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SymbbCoreConfigBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        // register extensions that do not follow the conventions manually
        $container->registerExtension(new SymbbCoreConfigExtension());
    }
}
