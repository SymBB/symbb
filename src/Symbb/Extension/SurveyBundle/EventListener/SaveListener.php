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

class SaveListener
{

    protected $em;

    /**
     *
     * @var \Symbb\Core\UserBundle\DependencyInjection\UserManager
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

            $repo = $this->em->getRepository('SymbbExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post->getId()));

            if (!$survey) {
                $survey = new \Symbb\Extension\SurveyBundle\Entity\Survey();
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

        $data = $this->getData($post);
        
        $event->addExtensionData('survey', $data);
    }

    protected function getAnswerArray(\Symbb\Extension\SurveyBundle\Entity\Survey $survey)
    {
        $answers = $survey->getAnswers();
        $answers = nl2br($answers, true);
        $answers = str_replace(array('<br />', '<br>'), ',', $answers);
        $answerList = \explode(',', $answers);
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
        $post = $topic->getMainPost();
        $data = $event->getExtensionData();
        $surveyQuestion = $data['survey']['question'];
        $surveyAnswers = $data['survey']['answers'];
        $surveyChoices = (int) $data['survey']['choices'];
        $surveyChoicesChangeable = (boolean) $data['survey']['choicesChangeable'];
        $surveyEnd = $data['survey']['end'];

        if (!empty($surveyQuestion) && !empty($surveyAnswers)) {

            $repo = $this->em->getRepository('SymbbExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post->getId()));

            if (!$survey) {
                $survey = new \Symbb\Extension\SurveyBundle\Entity\Survey();
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

        $data = $this->getData($post);

        $event->addExtensionData('survey', $data);
    }
    
    protected function getData(\Symbb\Core\ForumBundle\Entity\Post $post = null){
        
        $data = array(
            'question' => '',
            'answers' => '',
            'answersArray' => array(),
            'choices' => 1,
            'choicesChangeable' => true,
            'end' => 0,
            'myVote' => array(),
            'votable' => false
        );

        if (is_object($post) && $post->getId() > 0) {

            $repo = $this->em->getRepository('SymbbExtensionSurveyBundle:Survey');
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
                
                
                if($survey->getChoices() <= 1){
                    $data['myVote'] = null;
                    foreach ($data['answersArray'] as $key => $value) {
                        $vote = (int) $survey->checkForVote($key, $this->userManager->getCurrentUser());
                        if($vote === true){
                            $data['myVote'] = $key;
                            break;
                        }
                    }
                } else {
                    foreach ($data['answersArray'] as $key => $value) {
                        $data['myVote'][$key] = (int) $survey->checkForVote($key, $this->userManager->getCurrentUser());
                    }
                }
            }
        }
        
        return $data;
    }
}