<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{
    const CONFIGURE = 'symbb.core.site.navigation.menu_configure';

    private $factory;
    private $menu;

    /**
    * @param \Knp\Menu\FactoryInterface $factory
    * @param \Knp\Menu\ItemInterface $menu
    */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
    * @return \Knp\Menu\FactoryInterface
    */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
    * @return \Knp\Menu\ItemInterface
    */
    public function getMenu()
    {
        return $this->menu;
    }
}