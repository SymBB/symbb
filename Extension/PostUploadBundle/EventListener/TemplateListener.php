<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\PostUploadBundle\EventListener;

class TemplateListener
{

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;

    }

    public function addUploadTab($event)
    {
        $event->render('SymBBExtensionPostUploadBundle::tab.html.twig', array('form' => $event->getForm()));
    }

    public function addUploadTabContent($event)
    {
        $event->render('SymBBExtensionPostUploadBundle::tabcontent.html.twig', array('form' => $event->getForm()));
    }

    public function addTopicUploadTab($event)
    {
        $event->render('SymBBExtensionPostUploadBundle::tab.html.twig', array('form' => $event->getForm()));
    }

    public function addTopicUploadTabContent($event)
    {
        $event->render('SymBBExtensionPostUploadBundle::topicTabcontent.html.twig', array('form' => $event->getForm()));
    }

}