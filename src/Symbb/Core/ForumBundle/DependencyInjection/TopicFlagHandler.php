<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\DependencyInjection;

use \Symbb\Core\UserBundle\Entity\UserInterface;
use \Symbb\Core\ForumBundle\DependencyInjection\ForumFlagHandler;

class TopicFlagHandler extends \Symbb\Core\ForumBundle\DependencyInjection\AbstractFlagHandler
{

    /**
     * @var ForumFlagHandler 
     */
    protected $forumFlagHandler;

    protected $foundTopics = array();

    public function setForumFlagHandler(ForumFlagHandler $handler)
    {
        $this->forumFlagHandler = $handler;
    }

    public function removeFlag($object, $flag, UserInterface $user = null){
        parent::removeFlag($object, $flag, $user);
        // remove from all posts (childs)
        foreach($object->getPosts() as $subobject){
            parent::removeFlag($subobject, $flag, $user);
        }
        // remove from parents if the child is the only one with that flag
        $parent = $object->getForum();
        do {
            if(is_object($parent)){
                $topics = $parent->getTopics();
                $otherFlagFound = false;
                foreach($topics as $topic){
                    $otherFlagFound = $this->checkFlag($topic, $flag, $user);
                    if($otherFlagFound){
                        break;
                    }
                }
                if(!$otherFlagFound){
                    parent::removeFlag($parent, $flag, $user);
                }
            } else {
                break;
            }
        } while($parent = $parent->getParent());

    }

    public function insertFlag($object, $flag, UserInterface $user = null, $flushEm = true)
    {
        $ignore = false;

        // if we add a topic "new" flag, we need to check if the user has read access to the forum
        // an we must check if the user has ignore the forum
        if ($flag === 'new') {
            $access = $this->securityContext->isGranted('VIEW', $object->getForum(), $user);
            if ($access) {
                $ignore = $this->forumFlagHandler->checkFlag($object->getForum(), 'ignore', $user);
            } else {
                $ignore = true;
            }
        }

        if (!$ignore) {
            parent::insertFlag($object, $flag, $user, $flushEm);

            if($flag === 'new'){
                // insert to all parents ( recrusivly )
                $parent = $object->getForum();
                do {
                    if(is_object($parent)){
                        parent::insertFlag($parent, $flag, $user, $flushEm);
                    } else {
                        break;
                    }
                } while($parent = $parent->getParent());

                // insert to all posts (childs)
                foreach($object as $post){
                    parent::insertFlag($post, $flag, $user, $flushEm);
                }
            }
        }
    }
}
