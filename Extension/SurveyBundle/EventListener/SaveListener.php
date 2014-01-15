<?php

/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\SurveyBundle\EventListener;

use \SymBB\Core\EventBundle\Event\EditPostEvent;

class SaveListener {

    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function save(EditPostEvent $event) {

        $post = $event->getPost();
        $form = $event->getForm();
        $surveyQuestion = $form->get('surveyQuestion')->getData();
        $surveyAnswers = $form->get('surveyAnswers')->getData();
        $surveyChoices = (int)$form->get('surveyChoices')->getData();
        $surveyChoicesChangeable = (boolean)$form->get('surveyChoicesChangeable')->getData();
        $surveyEnd = $form->get('surveyEnd')->getData();

        if(!empty($surveyQuestion) && !empty($surveyAnswers)) {
            
            $repo = $this->em->getRepository('SymBBExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post->getId()));
            
            if (!$survey) {
                $survey = new \SymBB\Extension\SurveyBundle\Entity\Survey();
            }

            // if answers are changed, then we need to reset all votes because we have no unique answer keys
            if($surveyAnswers != $survey->getAnswers()){
                $votes = $survey->getVotes();
                foreach($votes as $vote){
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
        }
        //flush will be execute in the controller
    }

    public function handleRequest(EditPostEvent $event) {

        $post = $event->getPost();
        $form = $event->getForm();

        if ($post->getId() > 0) {

            $repo = $this->em->getRepository('SymBBExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post));

            if (is_object($survey)) {
                $form->get('surveyQuestion')->setData($survey->getQuestion());
                $form->get('surveyAnswers')->setData($survey->getAnswers());
                $form->get('surveyChoices')->setData($survey->getChoices());
                $form->get('surveyChoicesChangeable')->setData($survey->getChoicesChangeable());
                $form->get('surveyEnd')->setData($survey->getEnd());
            }
        }
    }

}