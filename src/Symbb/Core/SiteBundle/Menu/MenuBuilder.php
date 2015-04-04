<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symbb\Core\SiteBundle\Manager\SiteManager;
use Symbb\Core\SiteBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\Request;

class MenuBuilder
{
    use BuilderTrait;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMainMenu(Request $request, SiteManager $siteManager, $router)
    {

        $menu = $this->factory->createItem('root');
        $navi = $siteManager->getNavigation(null, 'main');
        $items = array();
        if (is_object($navi)) {
            $items = $navi->getItems();
        }
        $this->addChildren($menu, $items, $siteManager, $router);
        $this->eventDispatcher->dispatch(ConfigureMenuEvent::CONFIGURE, new ConfigureMenuEvent($this->factory, $menu));
        return $menu;
    }

    public function createFooterMenu(Request $request, SiteManager $siteManager, $router)
    {
        $menu = $this->factory->createItem('root');
        $navi = $siteManager->getNavigation(null, 'footer');
        $items = array();
        if (is_object($navi)) {
            $items = $navi->getItems();
        }
        $this->addChildren($menu, $items, $siteManager, $router);
        $this->eventDispatcher->dispatch(ConfigureMenuEvent::CONFIGURE, new ConfigureMenuEvent($this->factory, $menu));
        return $menu;
    }

}