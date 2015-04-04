<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Menu;

use Symbb\Core\SiteBundle\Manager\SiteManager;

trait BuilderTrait
{
    protected $factory;

    protected $eventDispatcher;

    protected function addChildren($menu, $children, SiteManager $siteManager, $router)
    {
        foreach ($children as $child) {
            if ($child->getType() == 'symfony') {
                try {
                    // generate directly to check if route realy exist
                    $uri = $router->generate($child->getSymfonyRoute(), $child->getSymfonyRouteParams());
                    //$childMenu = $menu->addChild($child->getTitle(), array('route' => $child->getSymfonyRoute(), 'routeParameters' => $child->getSymfonyRouteParams()));
                    $childMenu = $menu->addChild($child->getTitle(), array('uri' => $uri));
                } catch (\Exception $e) {

                }
            } else {
                $uri = $child->getFixUrl();
                $childMenu = $menu->addChild($child->getTitle(), array('uri' => $uri));
                if (strpos($uri, "www.") !== false || strpos($uri, "http") === 0) {
                    $sites = $siteManager->findAll();
                    $found = false;
                    foreach($sites as $site){
                        $domains = $site->getDomainArray();
                        foreach ($domains as $domain) {
                            $domain = str_replace(array('https://', 'http://', 'www.'), '', $domain);
                            if (!empty($uri) && !empty($domain) && strpos($uri, $domain) !== false) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if (!$found) {
                        $childMenu->setLinkAttributes(array('target' => '_blank'));
                    }
                }
            }
            if (isset($childMenu) && isset($child) && $child->hasChildren()) {
                $this->addChildren($childMenu, $child->getChildren(), $siteManager, $router);
            }
        }
    }
}