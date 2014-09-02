<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Menu;

use Knp\Menu\FactoryInterface;
use SymBB\Core\SiteBundle\DependencyInjection\SiteManager;
use SymBB\Core\SiteBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\Request;

class MenuBuilder
{
    private $factory;

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
        if(is_object($navi)){
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
        if(is_object($navi)){
            $items = $navi->getItems();
        }
        $this->addChildren($menu, $items, $siteManager, $router);
        $this->eventDispatcher->dispatch(ConfigureMenuEvent::CONFIGURE, new ConfigureMenuEvent($this->factory, $menu));
        return $menu;
    }

    protected function addChildren($menu, $children, SiteManager $siteManager, $router){
        foreach($children as $child){
            if($child->getType() == 'symfony'){
                try {
                    // generate directly to check if route realy exist
                    $uri = $router->generate($child->getSymfonyRoute(), $child->getSymfonyRouteParams());
                    //$childMenu = $menu->addChild($child->getTitle(), array('route' => $child->getSymfonyRoute(), 'routeParameters' => $child->getSymfonyRouteParams()));
                    $childMenu = $menu->addChild($child->getTitle(), array('uri' => $uri));
                } catch(\Exception $e) {

                }
            } else {
                $uri = $child->getFixUrl();
                $childMenu = $menu->addChild($child->getTitle(), array('uri' => $uri));
                if(strpos($uri, "www.") !== false || strpos($uri, "http") === 0){
                    $domains = $siteManager->getSite()->getDomainArray();
                    $found = false;
                    foreach($domains as $domain){
                        $domain = str_replace(array('https://', 'http://', 'www.'), '', $domain);
                        if(!empty($uri) && !empty($domain) && strpos($uri, $domain) !== false){
                            $found = true;
                            break;
                        }
                    }
                    if(!$found){
                        $childMenu->setLinkAttributes(array('target' => '_blank'));
                    }
                }
            }
            if($childMenu && $child->hasChildren()){
                $this->addChildren($childMenu, $child->getChildren(), $siteManager, $router);
            }
        }
    }
}