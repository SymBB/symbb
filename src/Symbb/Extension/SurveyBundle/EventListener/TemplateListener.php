<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\EventListener;

use Symbb\Core\EventBundle\Event\TemplateFormTopicEvent;
use Symbb\Core\EventBundle\Event\TemplatePostEvent;

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
        $form = $event->getForm();
        $event->render('SymbbExtensionSurveyBundle:Post:tabcontent.html.twig', array("form" => $form));
    }

    public function addTopicTab($event)
    {
        $event->render('SymbbExtensionSurveyBundle:Topic:tab.html.twig', array());
    }

    public function addTopicTabContent(TemplateFormTopicEvent $event)
    {
        $form = $event->getForm();
        $event->render('SymbbExtensionSurveyBundle:Topic:tabcontent.html.twig', array("form" => $form));
    }

    public function addSurveyBlock(TemplatePostEvent $event)
    {
        $post = $event->getPost();
        $repo = $this->em->getRepository('SymbbExtensionSurveyBundle:Survey');
        $survey = $repo->findOneBy(array('post' => $post));
        $event->render('SymbbExtensionSurveyBundle:Post:survey.html.twig', array('post' => $post, 'survey' => $survey));
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