<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\SurveyBundle\EventListener;

use SymBB\Extension\SurveyBundle\Security\Authorization\SurveyVoter;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ApiListener
{

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    public function __construct($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function postData(\SymBB\Core\EventBundle\Event\ApiDataEvent $event)
    {
        $post = $event->getObject();
        if(is_object($post)){
            $forum = $post->getTopic()->getForum();
            $createSurvey = $this->securityContext->isGranted(SurveyVoter::CREATE_SURVEY, $forum);
            $event->addAccessData('createSurvey', $createSurvey);
            $createSurvey = $this->securityContext->isGranted(SurveyVoter::VIEW_SURVEY, $forum);
            $event->addAccessData('viewSurvey', $createSurvey);
        }
    }

    public function topicData(\SymBB\Core\EventBundle\Event\ApiDataEvent $event)
    {
        $topic = $event->getObject();
        if(is_object($topic)){
            $forum = $topic->getForum();
            $createSurvey = $this->securityContext->isGranted(SurveyVoter::CREATE_SURVEY, $forum);
            $event->addAccessData('createSurvey', $createSurvey);
            $createSurvey = $this->securityContext->isGranted(SurveyVoter::VIEW_SURVEY, $forum);
            $event->addAccessData('viewSurvey', $createSurvey);
        }
    }

}