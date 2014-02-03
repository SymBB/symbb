<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\CalendarBundle\EventListener;

class TemplateListener
{
    
    public function stylesheets($event)
    {
        $event->render('SymBBExtensionCalendarBundle::stylesheets.html.twig', array());
    }
    
    public function javascripts($event)
    {
        $event->render('SymBBExtensionCalendarBundle::javascripts.html.twig', array());
    }

    public function addPostTab($event)
    {
        $event->render('SymBBExtensionCalendarBundle:Post:tab.html.twig', array('form' => $event->getForm()));

    }

    public function addPostTabContent($event)
    {
        $event->render('SymBBExtensionCalendarBundle:Post:tabcontent.html.twig', array('form' => $event->getForm()));

    }
}