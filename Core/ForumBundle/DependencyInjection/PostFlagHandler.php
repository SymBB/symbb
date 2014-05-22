<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\DependencyInjection;

use \SymBB\Core\UserBundle\Entity\UserInterface;

class PostFlagHandler extends \SymBB\Core\ForumBundle\DependencyInjection\AbstractFlagHandler
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
        }

    }

    public function findOne($flag, $object, UserInterface $user = null)
    {

        if (!$user) {
            $user = $this->getUser();
        }

        $flagObject = $this->em->getRepository('SymBBCoreForumBundle:Post\Flag', 'symbb')->findOneBy(array(
            'post' => $object->getId(),
            'user' => $user->getId(),
            'flag' => $flag
        ));

        return $flagObject;

    }

    public function findAll($object, UserInterface $user = null)
    {

        if (!$user) {
            $user = $this->getUser();
        }

        $flagObject = $this->em->getRepository('SymBBCoreForumBundle:Post\Flag', 'symbb')->findOneBy(array(
            'post' => $object,
            'user' => $user
        ));

        return $flagObject;

    }
    
    public function createNewFlag($object, UserInterface $user, $flag)
    {
        $flagObject = new \SymBB\Core\ForumBundle\Entity\Post\Flag();
        $flagObject->setPost($object);
        $flagObject->setUser($user);
        $flagObject->setFlag($flag);
        return $flagObject;

    }

    public function checkFlag($element, $flag, UserInterface $user = null)
    {
        $posts = $this->findPostsByFlag($flag, $element, $user, false);
        return count($posts);

    }

    protected function getMemcacheKey($flag, $post)
    {
        $key = 'symbb.post.' . $post->getId() . '.flag.' . $flag;
        return $key;

    }

    public function findFlagsByObjectAndFlag($object, $flag)
    {
        $flags = $this->em->getRepository('SymBBCoreForumBundle:Post\Flag', 'symbb')->findBy(array(
            'post' => $object,
            'flag' => $flag
        ));
        return $flags;

    }

    public function findPostsByFlag($flag, $element, $user = null, $objects = true, $limit = null)
    {
        $this->foundPost = array();
        $this->doFindPostsByFlag($flag, $element, $user, $objects, $limit);
        ksort($this->foundPost);
        return $this->foundPost;

    }

    public function doFindPostsByFlag($flag, $element, $user = null, $objects = true, $limit = null)
    {

        if (is_object($element)) {

            if (!$user) {
                $user = $this->getUser();
            }

            if (
                $user instanceof \SymBB\Core\UserBundle\Entity\UserInterface &&
                $user->getSymbbType() === 'user'
            ) {

                $elementClass = \Symfony\Component\Security\Core\Util\ClassUtils::getRealClass($element);

                if ($elementClass == 'SymBB\Core\ForumBundle\Entity\Post') {

                    if ($limit && count($this->foundPost) >= $limit) {
                        return $this->foundPost;
                    }

                    $users = $this->getUsersForFlag($flag, $element);
                    if (
                        isset($users[$user->getId()])
                    ) {
                        $timestamp = $users[$user->getId()];

                        if ($objects) {
                            $this->foundPost[$timestamp] = $element;
                        }
                        else {
                            $this->foundPost[$timestamp] = $element->getId();
                        }
                    }
                }
                else if ($elementClass == 'SymBB\Core\ForumBundle\Entity\Topic') {

                    $posts = $element->getPosts();

                    foreach ($posts as $post) {
                        if ($limit && count($this->foundPost) >= $limit) {
                            break;
                        }
                        $this->doFindPostsByFlag($flag, $post, $user, $objects, $limit);
                    }
                }
                else if ($elementClass == 'SymBB\Core\ForumBundle\Entity\Forum') {

                    $topics = $element->getTopics();

                    foreach ($topics as $topic) {
                        if ($limit && count($this->foundPost) >= $limit) {
                            break;
                        }
                        $this->doFindPostsByFlag($flag, $topic, $user, $objects, $limit);
                    }

                    $childs = $element->getChildren();
                    foreach ($childs as $child) {
                        if ($limit && count($this->foundPost) >= $limit) {
                            break;
                        }
                        $this->doFindPostsByFlag($flag, $child, $user, $objects, $limit);
                    }
                }
                else if (is_object($element)) {
                    throw new \Exception('findPostsByFlag don´t know the object (' . $elementClass . ')');
                }
            }
        }
        else if ($element === null) {
            $forumList = $this->em->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->findBy(array(
                'parent' => null
            ));
            foreach ($forumList as $forum) {
                $this->doFindPostsByFlag($flag, $forum, $user, $objects, $limit);
            }
        }


        return $this->foundPost;

    }
}
