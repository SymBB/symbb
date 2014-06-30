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

    public function createMainMenu(Request $request, SiteManager $siteManager)
    {
        $menu = $this->factory->createItem('root');
        $this->addChildren($menu, $siteManager->getNavigationItems(), $siteManager);
        $this->eventDispatcher->dispatch(ConfigureMenuEvent::CONFIGURE, new ConfigureMenuEvent($this->factory, $menu));
        return $menu;
    }

    protected function addChildren($menu, $children, SiteManager $siteManager){
        foreach($children as $child){
            if($child->getType() == 'symfony'){
                $childMenu = $menu->addChild($child->getTitle(), array('route' => $child->getSymfonyRoute(), 'routeParameters' => $child->getSymfonyRouteParams()));
            } else {
                $uri = $child->getFixUrl();
                $childMenu = $menu->addChild($child->getTitle(), array('uri' => $uri));
                if(strpos($uri, "www.") !== false || strpos($uri, "http") === 0){
                    $domains = $siteManager->getSite()->getDomainArray();
                    $found = false;
                    foreach($domains as $domain){
                        $domain = str_replace(array('https://', 'http://', 'www.'), '', $domain);
                        if(strpos($uri, $domain) !== false){
                            $found = true;
                            break;
                        }
                    }
                    if(!$found){
                        $childMenu->setLinkAttributes(array('target' => '_blank'));
                    }
                }
            }
            if($child->hasChildren()){
                $this->addChildren($childMenu, $child->getChildren(), $siteManager);
            }
        }
    }
}