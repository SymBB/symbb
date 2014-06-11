<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\RatingBundle\EventListener;

use SymBB\Extension\RatingBundle\Security\Authorization\RatingVoter;
use SymBB\Extension\SurveyBundle\Security\Authorization\SurveyVoter;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ApiListener
{

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    protected $em;

    public function __construct($securityContext, $em)
    {
        $this->securityContext = $securityContext;
        $this->em = $em;
    }

    public function postData(\SymBB\Core\EventBundle\Event\ApiDataEvent $event)
    {
        $post = $event->getObject();
        if(is_object($post)){
            $forum = $post->getTopic()->getForum();
            $createSurvey = $this->securityContext->isGranted(RatingVoter::CREATE_RATING, $forum);
            $event->addAccessData('createRating', $createSurvey);
            $createSurvey = $this->securityContext->isGranted(RatingVoter::VIEW_RATING, $forum);
            $event->addAccessData('viewRating', $createSurvey);
            $this->addExtensionData($event, $post);
        }
    }

    public function topicData(\SymBB\Core\EventBundle\Event\ApiDataEvent $event)
    {
        $topic = $event->getObject();
        if(is_object($topic)){
            $forum = $topic->getForum();
            $createSurvey = $this->securityContext->isGranted(RatingVoter::CREATE_RATING, $forum);
            $event->addAccessData('createRating', $createSurvey);
            $createSurvey = $this->securityContext->isGranted(RatingVoter::VIEW_RATING, $forum);
            $event->addAccessData('viewRating', $createSurvey);
            $this->addExtensionData($event, $topic->getMainPost());
        }
    }

    protected function addExtensionData($event, $post){

        $user = $this->securityContext->getToken()->getUser();

        $myLike = $this->em->getRepository('SymBBExtensionRatingBundle:Like')
            ->findOneBy(array('post' => $post, 'user' => $user));

        $myDislike = $this->em->getRepository('SymBBExtensionRatingBundle:Dislike')
            ->findOneBy(array('post' => $post, 'user' => $user));

        if(is_object($myLike)){
            $myLike = true;
        } else {
            $myLike = false;
        }

        if(is_object($myDislike)){
            $myDislike = true;
        } else {
            $myDislike = false;
        }

        $likes = $this->em->getRepository('SymBBExtensionRatingBundle:Like')
            ->findBy(array('post' => $post));

        $dislikes = $this->em->getRepository('SymBBExtensionRatingBundle:Dislike')
            ->findBy(array('post' => $post));

        $data = array(
            'like' => $myLike,
            'dislike' => $myDislike
        );

        $data['count'] = array(
            'like' => count($likes),
            'dislike' => count($dislikes)
        );

        $event->addExtensionData('rating', $data);
    }

}