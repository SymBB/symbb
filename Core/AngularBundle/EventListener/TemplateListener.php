<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\AngularBundle\EventListener;
use \SymBB\Core\AngularBundle\DependencyInjection\Router;

class TemplateListener
{
    
    /**
     * @var \SymBB\Core\AngularBundle\DependencyInjection\Router 
     */
    protected $router;
    
    /**
     * @param \SymBB\Core\AngularBundle\DependencyInjection\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    public function javascripts($event)
    {
        $event->render('SymBBCoreAngularBundle::javascripts.html.twig', array('angularRouter' => $this->router));
    }
}