<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\AngularBundle\Twig;

use Symbb\Core\AngularBundle\Routing\AngularRouter;

class RouterExtension extends \Twig_Extension
{

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;

    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getAngularRouter', array($this, 'getAngularRouter'))
        );

    }

    /**
     * 
     * @return AngularRouter
     */
    public function getAngularRouter()
    {
        return $this->container->get('symbb.core.angular.router');

    }


    public function getName()
    {
        return 'symbb_angular_router';

    }
}