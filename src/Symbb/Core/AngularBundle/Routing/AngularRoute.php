<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\AngularBundle\Routing;

class AngularRoute
{

    protected $pattern = '';

    protected $url = '';

    protected $controller = '';

    protected $section = '';

    protected $api = array('route' => '');

    protected $template = array('route' => '', 'params' => array());

    protected $router;

    protected $defaults = array();

    public function __construct($data, $router)
    {
        if (isset($data['pattern'])) {
            $this->pattern = $data['pattern'];
        }
        if (isset($data['controller'])) {
            $this->controller = $data['controller'];
        }
        if (isset($data['api'])) {
            $this->api = $data['api'];
        }
        if (isset($data['template'])) {
            $this->template = $data['template'];
        }
        if (isset($data['defaults'])) {
            $this->defaults = $data['defaults'];
        }
        if (isset($data['section'])) {
            $this->section = $data['section'];
        }
        $this->router = $router;
    }

    public function getSection(){
        return $this->section;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getAngularPattern()
    {
        $prefix = $this->router->generate('_symbb_index', array('_locale' => 'xxx'));
        $pattern = $this->getPattern();
        $pattern = \str_replace(array('{', '}'), array(':', '',), $pattern);
        $prefix = \str_replace(array('/xxx'), array(''), $prefix); // remove locale from prefix
        $prefix = \rtrim($prefix, '/');
        $pattern = $prefix . $pattern;
        return $pattern;
    }

    public function getAngularController()
    {
        return $this->controller;
    }

    public function getApiRoute()
    {
        return $this->api['route'];
    }

    public function getTemplateRoute()
    {
        return $this->template['route'];
    }

    public function getTemplateParams()
    {
        return $this->template['params'];
    }

    public function hasPattern()
    {
        if ($this->pattern !== "") {
            return true;
        }
        return false;
    }

    public function hasController()
    {
        if ($this->controller !== "") {
            return true;
        }
        return false;
    }

    public function hasApiRoute()
    {
        if ($this->api['route'] !== "") {
            return true;
        }
        return false;
    }

    public function hasTemplateRoute()
    {
        if ($this->template['route'] !== "") {
            return true;
        }
        return false;
    }
}