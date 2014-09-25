<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\BBCodeBundle\EventListener;

class TemplateListener
{
    
    public function stylesheets($event)
    {
        $event->render('SymBBCoreBBCodeBundle::stylesheets.html.twig', array());
    }
    
    public function javascripts($event)
    {
        $event->render('SymBBCoreBBCodeBundle::javascripts.html.twig', array());
    }
}