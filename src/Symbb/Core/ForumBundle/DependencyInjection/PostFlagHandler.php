<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\DependencyInjection;

use \Symbb\Core\UserBundle\Entity\UserInterface;

class PostFlagHandler extends \Symbb\Core\ForumBundle\DependencyInjection\AbstractFlagHandler
{

    protected $foundPost = array();

    /**
     * @var ForumFlagHandler 
     */
    protected $forumFlagHandler;

    public function setForumFlagHandler(ForumFlagHandler $handler)
    {
        $this->forumFlagHandler = $handler;

    }

    public function insertFlag($object, $flag, UserInterface $user = null, $flushEm = true)
    {
        $ignore = false;

        // if we add a post "new" flag, we need to check if the user has read access to the forum
        // an we must check if the user has ignore the forum
        if ($flag === 'new') {
            $access = $this->securityContext->isGranted('VIEW', $object->getTopic()->getForum(), $user);
            if ($access) {
                $ignore = $this->forumFlagHandler->checkFlag($object->getTopic()->getForum(), 'ignore', $user);
            }
            else {
                $ignore = true;
            }
        }

        if (!$ignore) {
            parent::insertFlag($object, $flag, $user, $flushEm);

            if($flag === 'new'){
                // insert to topic (parent)
                parent::insertFlag($object->getTopic(), $flag, $user, $flushEm);
                // insert to all parents ( recrusivly )
                $parent = $object->getTopic()->getForum();
                do {
                    if(is_object($parent)){
                        parent::insertFlag($parent, $flag, $user, $flushEm);
                    } else {
                        break;
                    }
                } while($parent = $parent->getParent());
            }
        }

    }

    /**
     * remove the flag an look recrusivily up if the parent has only this child with this flag
     * @param $object
     * @param $flag
     * @param UserInterface $user
     */
    public function removeFlag($object, $flag, UserInterface $user = null){
        parent::removeFlag($object, $flag, $user);
        $otherPostsHasThisFlag = false;
        foreach($object->getTopic()->getPosts() as $post){
            if($this->checkFlag($post, $flag, $user)){
                $otherPostsHasThisFlag = true;
                break;
            }
        }
        if(!$otherPostsHasThisFlag){
            parent::removeFlag($object->getTopic(), $flag, $user);
            $otherTopicsHasThisFlag = false;
            // remove from parents if the child is the only one with that flag
            $parent = $object->getTopic()->getForum();
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
    }
}
