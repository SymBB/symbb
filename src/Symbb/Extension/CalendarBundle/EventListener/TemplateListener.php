<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Symbb\Extension\CalendarBundle\EventListener;

class TemplateListener
{
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;
    
    public function __construct($em)
    {
        $this->em = $em;

    }


    public function stylesheets($event)
    {
        $event->render('SymbbExtensionCalendarBundle::stylesheets.html.twig', array());
    }
    
    public function javascripts($event)
    {
        $event->render('SymbbExtensionCalendarBundle::javascripts.html.twig', array());
    }

    public function addPostTab($event)
    {
        $event->render('SymbbExtensionCalendarBundle:Post:tab.html.twig', array('form' => $event->getForm()));

    }

    public function addPostTabContent($event)
    {
        $event->render('SymbbExtensionCalendarBundle:Post:tabcontent.html.twig', array('form' => $event->getForm()));

    }

    public function addTopicTab($event)
    {
        $event->render('SymbbExtensionCalendarBundle:Post:tab.html.twig', array('form' => $event->getForm()));

    }

    public function addTopicTabContent($event)
    {
        $event->render('SymbbExtensionCalendarBundle:Topic:tabcontent.html.twig', array('form' => $event->getForm()));

    }
    
    public function addPostEventBoxData(\Symbb\Core\EventBundle\Event\TemplatePostEvent $event)
    {
        $post = $event->getPost();
        $repo = $this->em->getRepository('SymbbExtensionCalendarBundle:Event');
        $calendarEvent = $repo->findOneBy(array('post' => $post));
        
        return $calendarEvent;
    }
    
    
    public function addPostEventBox(\Symbb\Core\EventBundle\Event\TemplatePostEvent $event)
    {
        $event->render('SymbbExtensionCalendarBundle:Post:event.html.twig', array('calendarEvent' => array()));
    }
}