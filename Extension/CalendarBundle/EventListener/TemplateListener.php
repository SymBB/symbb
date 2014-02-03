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

    public function addPostEventBox(\SymBB\Core\EventBundle\Event\TemplatePostEvent $event)
    {

        $post = $event->getPost();
        $repo = $this->em->getRepository('SymBBExtensionCalendarBundle:Event');
        $calendarEvent = $repo->findOneBy(array('post' => $post));
        
        $event->render('SymBBExtensionCalendarBundle:Post:event.html.twig', array('calendarEvent' => $calendarEvent));

    }
}