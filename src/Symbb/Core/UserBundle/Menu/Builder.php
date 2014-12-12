<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{

    
    public function optionMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Profil', array('route' => 'symbb_user_options', 'routeParameters' => array()))->setExtra('translation_domain', 'symbb_frontend');
        $menu->addChild('Security', array('route' => 'symbb_user_options_security'))->setExtra('translation_domain', 'symbb_frontend');

        return $menu;

    }
    
    
}