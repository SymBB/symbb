<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\SurveyBundle\EventListener;

class TemplateListener
{

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function addPostTab($event)
    {
        $event->render('SymBBExtensionSurveyBundle:Post:tab.html.twig', array());
    }

    public function addPostTabContent($event)
    {
        $event->render('SymBBExtensionSurveyBundle:Post:tabcontent.html.twig', array());
    }

    public function addTopicTab($event)
    {
        $this->addPostTab($event);
    }

    public function addTopicTabContent($event)
    {
        $event->render('SymBBExtensionSurveyBundle:Topic:tabcontent.html.twig', array());
    }

    public function addSurveyBlockData($event)
    {
        $post = $event->getPost();
        $repo = $this->em->getRepository('SymBBExtensionSurveyBundle:Survey');
        $survey = $repo->findOneBy(array('post' => $post));
        return array('post' => $post, 'survey' => $survey);
    }

    public function addSurveyBlock($event)
    {
        $event->render('SymBBExtensionSurveyBundle:Post:survey.html.twig', array('post' => array(), 'survey' => array()));
    }

    public function stylesheets($event)
    {
        $event->render('SymBBExtensionSurveyBundle::stylesheets.html.twig', array());
    }

    public function javascripts($event)
    {
        $event->render('SymBBExtensionSurveyBundle::javascripts.html.twig', array());
    }
}