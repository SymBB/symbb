<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AngularBundle\DependencyInjection;

use Symfony\Component\Yaml\Parser;
use \SymBB\Core\AngularBundle\Routing\AngularRoute;

class Router
{

    /**
     *
     * @var array(<\SymBB\Core\AngularBundle\Routing\AngularRoute>)
     */
    protected $frontend = array();

    protected $eventDispatcher;

    public function __construct($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->loadData();
    }

    public function getFiles()
    {
        $files = array();
        $files[] = __DIR__ . '/../Resources/config/routing/frontend.yml';

        $event = new \SymBB\Core\AngularBundle\Event\RouterFilesEvent($files);
        $this->eventDispatcher->dispatch('symbb.core.angular.router.files', $event);

        return $event->getFiles();
    }

    public function loadData()
    {
        $yaml = new Parser();
        foreach ($this->getFiles() as $file) {
            $frontend = $yaml->parse(file_get_contents($file));
            foreach ($frontend as $key => $data) {
                $this->frontend['angular_locale_' . $key] = new AngularRoute($data);
                
            }
        }
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

            $key = \str_replace(array('angular_locale_', 'angular_'), '', $key);

            if (!isset($data[$key])) {
                $data[$key] = array();
            }

            if ($routing->hasPattern()) {
                if (!isset($data[$key]['pattern'])) {
                    $data[$key]['pattern'] = array();
                }
                $data[$key]['pattern'][] = $routing->getAngularPattern();
                $data[$key]['controller'] = $routing->getAngularController();
            }
        }
        return \json_encode($data);
    }

    public function createSfApiRouteJson()
    {
        $data = array();
        foreach ($this->getFrontendRouting() as $key => $routing) {
            $key = \str_replace(array('angular_locale_', 'angular_'), '', $key);
            if (!isset($data[$key])) {
                $data[$key] = array();
            }
            if ($routing->hasApiRoute()) {
                $data[$key]['route'] = $routing->getApiRoute();
            }
        }
        return \json_encode($data);
    }

    public function createSfTemplateRouteJson()
    {
        $data = array();
        foreach ($this->getFrontendRouting() as $key => $routing) {
            $key = \str_replace(array('angular_locale_', 'angular_'), '', $key);
            if (!isset($data[$key])) {
                $data[$key] = array();
            }
            if ($routing->hasTemplateRoute()) {
                $data[$key]['route'] = $routing->getTemplateRoute();
                $data[$key]['params'] = $routing->getTemplateParams();
            }
        }
        return \json_encode($data);
    }
}