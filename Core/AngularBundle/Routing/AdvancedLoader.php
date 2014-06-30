<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AngularBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use \SymBB\Core\AngularBundle\DependencyInjection\Router;

class AdvancedLoader extends Loader
{

    /**
     *
     * @var \SymBB\Core\AngularBundle\DependencyInjection\Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        // create a route for every angular route
        foreach ($this->router->getFrontendRouting() as $routeName => $routing) {
            if ($routing->hasPattern()) {
                // prepare a new route
                $pattern = $routing->getPattern();
                $defaults = $routing->getDefaults();
                $defaults['_controller'] = 'SymBBCoreAngularBundle:Angular:index';
                $requirements = array();
                $route = new Route($pattern, $defaults, $requirements);
                // add the new route to the route collection:
                $routes->add($routeName, $route);
            }
        }

        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return true;
    }
}