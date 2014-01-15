<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\MessageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Neue Nachricht', array('route' => 'fos_message_thread_new'))->setExtra('translation_domain', 'symbb_frontend');
        $menu->addChild('Posteingang', array('route' => 'fos_message_inbox'))->setExtra('translation_domain', 'symbb_frontend');
        $menu->addChild('Postausgang', array('route' => 'fos_message_sent'))->setExtra('translation_domain', 'symbb_frontend');

        return $menu;
    }
}