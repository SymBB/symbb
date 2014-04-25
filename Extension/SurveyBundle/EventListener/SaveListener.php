<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\SurveyBundle\EventListener;

use \SymBB\Core\EventBundle\Event\ApiSaveEvent;
use \SymBB\Core\EventBundle\Event\ApiDataEvent;

class SaveListener
{

    protected $em;

    /**
     *
     * @var \SymBB\Core\UserBundle\DependencyInjection\UserManager
     */
    protected $userManager;

    public function __construct($em, $userManager)
    {
        $this->em = $em;
        $this->userManager = $userManager;
    }

    public function savePost(ApiSaveEvent $event)
    {

        $post = $event->getObject();
        $data = $event->getExtensionData();
        $surveyQuestion = $data['survey']['question'];
        $surveyAnswers = $data['survey']['answers'];
        $surveyChoices = (int) $data['survey']['choices'];
        $surveyChoicesChangeable = (boolean) $data['survey']['choicesChangeable'];
        $surveyEnd = $data['survey']['end'];
        if (!empty($surveyQuestion) && !empty($surveyAnswers)) {

            $repo = $this->em->getRepository('SymBBExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post->getId()));

            if (!$survey) {
                $survey = new \SymBB\Extension\SurveyBundle\Entity\Survey();
            }

            // if answers are changed, then we need to reset all votes because we have no unique answer keys
            if ($surveyAnswers != $survey->getAnswers()) {
                $votes = $survey->getVotes();
                foreach ($votes as $vote) {
                    $this->em->remove($vote);
                }
                $survey->setVotes(array());
            }

            $survey->setAnswers($surveyAnswers);
            $survey->setQuestion($surveyQuestion);
            $survey->setChoices($surveyChoices);
            $survey->setChoicesChangeable($surveyChoicesChangeable);
            $survey->setPost($post);
            $survey->setEnd($surveyEnd);
            
            $this->em->persist($survey);
            $this->em->flush();
        }
        //flush will be execute in the controller but only if main object are changed...
    }

    public function dataPost(ApiDataEvent $event)
    {

        $post = $event->getObject();

        $data = array(
            'id' => 0,
            'question' => '',
            'answers' => '',
            'answersArray' => array(),
            'choices' => 1,
            'choicesChangeable' => true,
            'end' => 0,
            'votable' => false,
            'myVote' => array()
        );

        if ($post->getId() > 0) {

            $repo = $this->em->getRepository('SymBBExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post));

            if (is_object($survey)) {
                $data = array(
                    'id' => $survey->getId(),
                    'question' => $survey->getQuestion(),
                    'answers' => $survey->getAnswers(),
                    'answersArray' => $this->getAnswerArray($survey),
                    'choices' => $survey->getChoices(),
                    'choicesChangeable' => $survey->getChoicesChangeable(),
                    'end' => $survey->getEnd(),
                    'votable' => $survey->checkIfVoteable($this->userManager->getCurrentUser())
                );
                foreach ($data['answersArray'] as $key => $value) {
                    $data['myVote'][$key] = (int) $survey->checkForVote($key, $this->userManager->getCurrentUser());
                }
            }
        }

        $event->addExtensionData('survey', $data);
    }

    protected function getAnswerArray(\SymBB\Extension\SurveyBundle\Entity\Survey $survey)
    {
        $answerList = \explode(',', $survey->getAnswers());

        $return = array();
        foreach ($answerList as $key => $answer) {
            $type = 'radio';
            if ($survey->getChoices() > 1) {
                $type = 'checkbox';
            }
            $return[] = array(
                'name' => $answer,
                'percent' => $survey->getAnswerPercent($key),
                'key' => $key,
                'type' => $type
            );
        }
        return $return;
    }

    public function saveTopic(ApiSaveEvent $event)
    {

        $topic = $event->getObject();
        $post = $topic->getMainObject();
        $data = $event->getExtensionData();
        $surveyQuestion = $data['survey']['question'];
        $surveyAnswers = $data['survey']['answers'];
        $surveyChoices = (int) $data['survey']['choices'];
        $surveyChoicesChangeable = (boolean) $data['survey']['choicesChangeable'];
        $surveyEnd = $data['survey']['end'];

        if (!empty($surveyQuestion) && !empty($surveyAnswers)) {

            $repo = $this->em->getRepository('SymBBExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post->getId()));

            if (!$survey) {
                $survey = new \SymBB\Extension\SurveyBundle\Entity\Survey();
            }

            // if answers are changed, then we need to reset all votes because we have no unique answer keys
            if ($surveyAnswers != $survey->getAnswers()) {
                $votes = $survey->getVotes();
                foreach ($votes as $vote) {
                    $this->em->remove($vote);
                }
                $survey->setVotes(array());
            }

            $survey->setAnswers($surveyAnswers);
            $survey->setQuestion($surveyQuestion);
            $survey->setChoices($surveyChoices);
            $survey->setChoicesChangeable($surveyChoicesChangeable);
            $survey->setPost($post);
            $survey->setEnd($surveyEnd);

            $this->em->persist($survey);
            $this->em->flush();
        }
        //flush will be execute in the controller but only if main object are changed...
    }

    public function dataTopic(ApiDataEvent $event)
    {

        $topic = $event->getObject();
        $post = $topic->getMainPost();

        $data = array(
            'question' => '',
            'answers' => '',
            'answersArray' => array(),
            'choices' => 1,
            'choicesChangeable' => true,
            'end' => 0,
            'myVote' => array()
        );

        if ($post->getId() > 0) {

            $repo = $this->em->getRepository('SymBBExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post));

            if (is_object($survey)) {
                $dateFormater = $this->userManager->getDateFormater('FULL');
 
                $data = array(
                    'question' => $survey->getQuestion(),
                    'answers' => $survey->getAnswers(),
                    'answersArray' => $this->getAnswerArray($survey),
                    'choices' => $survey->getChoices(),
                    'choicesChangeable' => $survey->getChoicesChangeable(),
                    'end' => 0,
                );
                foreach ($data['answersArray'] as $key => $value) {
                    $data['myVote'][$key] = (int) $survey->checkForVote($key, $this->userManager->getCurrentUser());
                }
            }
        }

        $event->addExtensionData('survey', $data);
    }
}