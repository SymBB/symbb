<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AngularBundle\Controller;

use SymBB\Core\AngularBundle\DependencyInjection\Router;
use SymBB\Core\AngularBundle\Routing\AngularRoute;
use Symfony\Component\HttpFoundation\Request;

class AngularController extends \SymBB\Core\SystemBundle\Controller\AbstractController
{
    public function indexAction(Request $request)
    {
        $userAgent = $request->server->get("HTTP_USER_AGENT");

        if (strpos($userAgent, 'Google') !== false || $request->get('seo') == 1) {
            return $this->seoAction($request);
        } else {
            return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array());
        }

    }

    public function seoAction(Request $request)
    {
        $router = $this->get("router");
        $route = $router->match($this->getRequest()->getPathInfo());

        $symbbRouter = $this->get('symbb.core.angular.router');
        /**
         * @var Router $symbbRouter
         */
        $routeData = $symbbRouter->getFrontendRouting();

        if(isset($routeData[$route['_route']])){

            $angularRoute = $routeData[$route['_route']];
            /**
             * @var AngularRoute $angularRoute
             */
            $routeCollection = $router->getRouteCollection();
            $templateRoute = $routeCollection->get($angularRoute->getTemplateRoute());

            if(is_object($templateRoute)){

                $controller = $templateRoute->getDefault('_controller');

                if(!empty($controller)){

                    $controllerCall = $this->generateControllerCallName($controller);

                    $data = array();

                    if(!empty($controllerCall) ){

                        $params = $angularRoute->getTemplateParams();

                        $response = $this->forward($controllerCall, $params);
                        $html = $response->getContent();

                        $converter = $this->get('symbb.core.angular.to.twig.converter');
                        $converter->setHtml($html);
                        $converter->setParentTemplate('::layout.html.twig');
                        $twigHtml = $converter->convert();

                        $apiRoute = $routeCollection->get($angularRoute->getApiRoute());
                        if(is_object($apiRoute)){
                            $apiController = $apiRoute->getDefault('_controller');
                            foreach($route as $key => $value){
                                $request->request->set($key, $value);
                            }
                            $apiControllerCall = $this->generateControllerCallName($apiController, array());

                            if(!empty($apiControllerCall) ){
                                $response = $this->forward($apiControllerCall, array());
                                $json = $response->getContent();
                                $data = json_decode($json, true);
                            }
                        }

                        $data['template'] = $twigHtml;

                        return $this->render($this->getTemplateBundleName('forum') . ':Angular:convertToTwig.html.twig', $data);
                    }
                }
            }
        }
    }

    protected function generateControllerCallName($controller){
        $tmp = explode('::', $controller);
        $controllerAction = end($tmp);
        $controllerAction = str_replace('Action', '', $controllerAction);
        $namespace = reset($tmp);
        $tmp = explode('\\', $namespace);
        $controlelrName = end($tmp);
        $controlelrName = str_replace('Controller', '', $controlelrName);

        // Search Bundle Name for Template Controller
        $bundles = $this->get('kernel')->getBundles();
        $bundleName = '';

        foreach($bundles as $type=>$bundle){
            $className = get_class($bundle);
            $entityClass = substr($namespace,0,strpos($namespace,'\\Controller\\'));

            if(strpos($className,$entityClass) !== FALSE) {
                $bundleName = $type;
            }
        }

        $controllerCall = '';

        if(!empty($bundleName) && !empty($controlelrName) && !empty($controllerAction)){
            $controllerCall = $bundleName .':'.$controlelrName.':'.$controllerAction;
        }

        return $controllerCall;
    }

}