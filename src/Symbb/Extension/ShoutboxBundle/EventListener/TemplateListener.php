<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Symbb\Extension\ShoutboxBundle\EventListener;

use \Symbb\Core\EventBundle\Event\TemplateDefaultEvent;

class TemplateListener
{
    public function javascripts(TemplateDefaultEvent $event){
        $event->render('SymbbExtensionShoutboxBundle::javascripts.html.twig', array());
    }

    public function stylesheets(TemplateDefaultEvent $event){
        $event->render('SymbbExtensionShoutboxBundle::stylesheets.html.twig', array());
    }

    public function renderBox(TemplateDefaultEvent $event){
        $event->render('SymbbExtensionShoutboxBundle:Shoutbox:small.html.twig', array());
    }
}