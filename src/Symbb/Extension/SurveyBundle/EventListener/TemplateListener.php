<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\EventListener;

class TemplateListener
{

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function addPostTab($event)
    {
        $event->render('SymbbExtensionSurveyBundle:Post:tab.html.twig', array());
    }

    public function addPostTabContent($event)
    {
        $event->render('SymbbExtensionSurveyBundle:Post:tabcontent.html.twig', array());
    }

    public function addTopicTab($event)
    {
        $event->render('SymbbExtensionSurveyBundle:Topic:tab.html.twig', array());
    }

    public function addTopicTabContent($event)
    {
        $event->render('SymbbExtensionSurveyBundle:Topic:tabcontent.html.twig', array());
    }

    public function addSurveyBlockData($event)
    {
        $post = $event->getPost();
        $repo = $this->em->getRepository('SymbbExtensionSurveyBundle:Survey');
        $survey = $repo->findOneOrNullBy(array('post' => $post));
        return array('post' => $post, 'survey' => $survey);
    }

    public function addSurveyBlock($event)
    {
        $event->render('SymbbExtensionSurveyBundle:Post:survey.html.twig', array('post' => array(), 'survey' => array()));
    }

    public function stylesheets($event)
    {
        $event->render('SymbbExtensionSurveyBundle::stylesheets.html.twig', array());
    }

    public function javascripts($event)
    {
        $event->render('SymbbExtensionSurveyBundle::javascripts.html.twig', array());
    }
}