<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\EventListener;

use \Symbb\Core\EventBundle\Event\ApiSaveEvent;
use \Symbb\Core\EventBundle\Event\ApiDataEvent;
use Symbb\Core\ForumBundle\Event\PostFormSaveEvent;
use Symbb\Core\ForumBundle\Event\TopicFormSaveEvent;

class SaveListener
{

    protected $em;

    /**
     *
     * @var \Symbb\Core\UserBundle\Manager\UserManager
     */
    protected $userManager;

    public function __construct($em, $userManager)
    {
        $this->em = $em;
        $this->userManager = $userManager;
    }

    public function savePost(PostFormSaveEvent $event)
    {
        $post = $event->getPost();

        $data = $event->getForm()->get("extensionSurvey");
        $survey = $data->getData();

        if ($survey->getQuestion() != "" && $survey->getAnswers() != "") {
            $survey->setPost($post);
            $this->em->persist($survey);
            $this->em->flush();
        } else if($survey->getId() > 0) {
            $this->em->remove($survey);
            $this->em->flush();
        }
    }

    public function saveTopic(TopicFormSaveEvent $event)
    {

        $topic = $event->getTopic();
        $post = $topic->getMainPost();

        $data = $event->getForm()->get("mainPost")->get("extensionSurvey");
        $survey = $data->getData();

        if ($survey->getQuestion() != "" && $survey->getAnswers() != "") {
            $survey->setPost($post);
            $this->em->persist($survey);
            $this->em->flush();
        } else if($survey->getId() > 0) {
            $this->em->remove($survey);
            $this->em->flush();
        }

    }
}