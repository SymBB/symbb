<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\DependencyInjection;

use \SymBB\Core\UserBundle\Entity\UserInterface;
use \SymBB\Core\ForumBundle\DependencyInjection\ForumFlagHandler;

class TopicFlagHandler extends \SymBB\Core\ForumBundle\DependencyInjection\AbstractFlagHandler
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

    public function insertFlag($object, $flag, UserInterface $user = null, $flushEm = true)
    {
        $ignore = false;

        // if we add a topic "new" flag, we need to check if the user has read access to the forum
        // an we must check if the user has ignore the forum
        if ($flag === 'new') {
            $this->accessManager->addAccessCheck('SYMBB_FORUM#VIEW', $object->getForum(), $user);
            $access = $this->accessManager->hasAccess();
            if ($access) {
                $ignore = $this->forumFlagHandler->checkFlag($object->getForum(), 'ignore', $user);
            } else {
                $ignore = true;
            }
        }

        if (!$ignore) {
            parent::insertFlag($object, $flag, $user, $flushEm);
        }
    }

    public function findOne($flag, $object, UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }

        $flagObject = $this->em->getRepository('SymBBCoreForumBundle:Topic\Flag', 'symbb')->findOneBy(array(
            'topic' => $object,
            'user' => $user,
            'flag' => $flag
        ));

        return $flagObject;
    }
    
    public function findAll($object, UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }

        $flagObject = $this->em->getRepository('SymBBCoreForumBundle:Topic\Flag', 'symbb')->findBy(array(
            'topic' => $object,
            'user' => $user
        ));

        return $flagObject;
    }

    public function createNewFlag($object, UserInterface $user, $flag)
    {
        $flagObject = new \SymBB\Core\ForumBundle\Entity\Topic\Flag();
        $flagObject->setTopic($object);
        $flagObject->setUser($user);
        $flagObject->setFlag($flag);
        return $flagObject;
    }

    public function checkFlag($element, $flag, UserInterface $user = null)
    {
        $topics = $this->findTopicsByFlag($flag, $element, $user, false);
        return count($topics);
    }

    public function findTopicsByFlag($flag, $element, $user = false, $objects = true, $limit = null)
    {
        $this->foundTopics = array();
        $this->doFindTopicsByFlag($flag, $element, $user, $objects, $limit);
        ksort($this->foundTopics);
        return $this->foundTopics;
    }

    public function doFindTopicsByFlag($flag, $element, $user = false, $objects = true, $limit = null)
    {

        $foundTopics = array();

        if (is_object($element)) {

            if (!$user) {
                $user = $this->getUser();
            }

            if (
                $user instanceof \SymBB\Core\UserBundle\Entity\UserInterface &&
                $user->getSymbbType() === 'user'
            ) {

                $elementClass = \Symfony\Component\Security\Core\Util\ClassUtils::getRealClass($element);

                if ($elementClass == 'SymBB\Core\ForumBundle\Entity\Topic') {

                    if ($limit && count($this->foundTopics) >= $limit) {
                        return $this->foundTopics;
                    }

                    $users = $this->getUsersForFlag($flag, $element);

                    if (
                        isset($users[$user->getId()])
                    ) {
                        $timestamp = $users[$user->getId()];

                        if ($objects) {
                            $this->foundTopics[$timestamp] = $element;
                        } else {
                            $this->foundTopics[$timestamp] = $element->getId();
                        }
                    }
                } else if ($elementClass == 'SymBB\Core\ForumBundle\Entity\Forum') {

                    $topics = $element->getTopics();

                    foreach ($topics as $topic) {
                        if ($limit && count($this->foundTopics) >= $limit) {
                            break;
                        }
                        $this->doFindTopicsByFlag($flag, $topic, $user, $objects, $limit);
                    }

                    $childs = $element->getChildren();
                    foreach ($childs as $child) {
                        if ($limit && count($this->foundTopics) >= $limit) {
                            break;
                        }
                        $this->doFindTopicsByFlag($flag, $child, $user, $objects, $limit);
                    }
                } else if ($elementClass == 'SymBB\Core\ForumBundle\Entity\Post') {

                    $topic = $element->getTopic();
                    $this->doFindTopicsByFlag($flag, $topic, $user, $objects, $limit);
                } else if (is_object($element)) {
                    throw new \Exception('findTopicsByFlag don´t know the object (' . $elementClass . ')');
                }
            }
        }

        return $foundTopics;
    }

    protected function getMemcacheKey($flag, $topic)
    {
        $key = 'symbb.topic.' . $topic->getId() . '.flag.' . $flag;
        return $key;
    }

    public function findFlagsByObjectAndFlag($object, $flag)
    {
        $flags = $this->em->getRepository('SymBBCoreForumBundle:Topic\Flag', 'symbb')->findBy(array(
            'topic' => $object,
            'flag' => $flag
        ));
        return $flags;
    }
}
