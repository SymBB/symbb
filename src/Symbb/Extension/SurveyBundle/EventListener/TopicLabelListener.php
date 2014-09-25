<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\SurveyBundle\EventListener;

class TopicLabelListener
{

    protected $em;
    
    public function __construct($em) {
        $this->em = $em;
    }
    
    public function topicLabels(\SymBB\Core\EventBundle\Event\TopicLabelsEvent $event){
        
        $topic = $event->getTopic();
        $posts = $topic->getPosts();
        
        foreach($posts as $post){
            $repo   = $this->em->getRepository('SymBBExtensionSurveyBundle:Survey');
            $survey = $repo->findOneBy(array('post' => $post));
            if(\is_object($survey)){
                $event->addLabel(array(
                    'title' => 'survey',
                    'type' => 'default'
                ));
                break;
            }
        }
        
    }
}