<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AngularBundle\Routing;

use Symfony\Component\Yaml\Parser;
use \SymBB\Core\AngularBundle\Routing\AngularRoute;

class AngularRouter
{

    /**
     *
     * @var array(<\SymBB\Core\AngularBundle\Routing\AngularRoute>)
     */
    protected $frontend = array();

    protected $eventDispatcher;

    protected $sfRouter;

    public function __construct($eventDispatcher, $router){
        $this->eventDispatcher = $eventDispatcher;
        $this->sfRouter = $router;
        $this->load();
    }

    public function load(){
        $sfRoutes = $this->sfRouter->getRouteCollection();
        foreach($sfRoutes as $routeKey => $currRoute){
            $options = $currRoute->getOptions();
            // if a symbb angular option is set
            // configure the route

            if(
                array_key_exists('symbb_angular_api', $options) ||
                array_key_exists('symbb_angular_controller', $options) ||
                array_key_exists('symbb_angular_template', $options)
            ){
                $defaults = $currRoute->getDefaults();
                $defaults['_controller'] = 'SymBBCoreAngularBundle:Angular:index';
                $currRoute->setDefaults($defaults);
                // If no api route and no controller is provided
                // used default controller without api call
                if(
                    !isset($options['symbb_angular_api_route']) &&
                    !isset($options['symbb_angular_controller'])
                ){
                    $options['symbb_angular_controller'] = 'DefaultController';
                    // if only controller is missing
                    // use DefaultApiController
                } else if(
                    !isset($options['symbb_angular_controller']) &&
                    isset($options['symbb_angular_api_route'])
                ){
                    $options['symbb_angular_controller'] = 'DefaultApiController';
                }
                $currRoute->setOptions($options);
                $this->addRoute($currRoute, $routeKey);
            }
        }
    }

    /**
     * @param $route
     * @param $key
     */
    public function addRoute($currRoute, $routeKey) {
        $pattern = $currRoute->getPattern();
        $defaults = $currRoute->getDefaults();
        $options = $currRoute->getOptions();
        $apiRoute = '';
        $templateOptions = array();
        $controller = '';
        if(isset($options['symbb_angular_api_route'])){
            $apiRoute = $options['symbb_angular_api_route'];
        }
        if(isset($options['symbb_angular_template'])){
            $templateOptions = $options['symbb_angular_template'];
        }
        if(isset($options['symbb_angular_controller'])){
            $controller = $options['symbb_angular_controller'];
        }
        $angularRoute = new AngularRoute(
            array(
                'pattern' => $pattern,
                'controller' => $controller,
                'api' => array('route' => $apiRoute),
                'template' => $templateOptions,
                'defaults' => $defaults
            ),
            $this->sfRouter)
        ;
        $this->frontend[$routeKey] = $angularRoute;
    }

    /**
     * @return array(<\SymBB\Core\AngularBundle\Routing\AngularRoute>)
     */
    public function getFrontendRouting()
    {
        return $this->frontend;
    }

    public function createAngularRouteJson()
    {
        $data = array();
        foreach ($this->getFrontendRouting() as $key => $routing) {

            $key = \str_replace(array('angular_'), '', $key);

            if (!isset($data[$key])) {
                $data[$key] = array();
            }

            if ($routing->hasPattern()) {
                if (!isset($data[$key]['pattern'])) {
                    $data[$key]['pattern'] = array();
                }
                $data[$key]['pattern'][] = $routing->getAngularPattern();
                $data[$key]['controller'] = $routing->getAngularController();
                $data[$key]['defaults'] = $routing->getDefaults();
                if($routing->hasTemplateRoute()){
                    $data[$key]['template'] = array(
                        'route' => $routing->getTemplateRoute(),
                        'params' => $routing->getTemplateParams()
                    );
                }
                if($routing->hasApiRoute()){
                    $data[$key]['api'] = array(
                        'route' => $routing->getApiRoute()
                    );
                }
            }
        }
        return \json_encode($data);
    }

}