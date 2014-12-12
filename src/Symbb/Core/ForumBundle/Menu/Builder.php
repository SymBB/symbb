<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Symbb\Core\ForumBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function subMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Übersicht', array('route' => '_symbbcoreforumbundle_forum_list'))->setExtra('translation_domain', 'menu');
        $menu->addChild('Neues Forum', array('route' => '_symbbcoreforumbundle_forum_new'))->setExtra('translation_domain', 'menu');
        $menu->addChild('Neue Kategorie', array('route' => '_symbbcoreforumbundle_forum_new_category'))->setExtra('translation_domain', 'menu');
        $menu->addChild('Neuer Link', array('route' => '_symbbcoreforumbundle_forum_new_link'))->setExtra('translation_domain', 'menu');

        return $menu;
    }
}