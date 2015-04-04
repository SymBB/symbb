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
use Symbb\Core\SiteBundle\Event\ConfigureMenuEvent;
use Symfony\Component\DependencyInjection\ContainerAware;

class DynamicBuilder extends ContainerAware
{
    use BuilderTrait;

    public function createMenu(FactoryInterface $factory, array $options){

        $this->factory = $factory;
        $siteManager = $this->container->get('symbb.core.site.manager');
        $router = $this->container->get('router');
        $this->eventDispatcher = $this->container->get('event_dispatcher');

        $name = $options["name"];
        $menu = $this->factory->createItem('root');
        $navi = $siteManager->getNavigation(null, $name);
        $items = array();
        if (is_object($navi)) {
            $items = $navi->getItems();
        }
        $this->addChildren($menu, $items, $siteManager, $router);
        $this->eventDispatcher->dispatch(ConfigureMenuEvent::CONFIGURE, new ConfigureMenuEvent($this->factory, $menu));
        return $menu;
    }

}