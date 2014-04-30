<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AngularBundle\Routing;

class AngularRoute
{

    protected $pattern = '';

    protected $url = '';

    protected $controller = '';

    protected $api = array('route' => '');

    protected $template = array('route' => '', 'params' => array());

    public function __construct($data)
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
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getAngularPattern()
    {
        $pattern = $this->getPattern();
        $pattern = \str_replace(array('{', '}'), array(':', ''), $pattern);
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