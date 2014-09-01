<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\AngularBundle\EventListener;
use \SymBB\Core\AngularBundle\Routing\AngularRouter;

class TemplateListener
{
    
    /**
     * @var \SymBB\Core\AngularBundle\Routing\AngularRouter
     */
    protected $router;
    
    /**
     * @param \SymBB\Core\AngularBundle\Routing\AngularRouter $router
     */
    public function __construct(AngularRouter $router)
    {
        $this->router = $router;
    }
    
    public function javascripts($event)
    {
        $event->render('SymBBCoreAngularBundle::javascripts.html.twig', array('angularRouter' => $this->router));
    }
}