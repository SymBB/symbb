<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\DependencyInjection;

use Symbb\Core\ForumBundle\Entity\Forum;
use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\SystemBundle\Manager\AbstractFlagHandler;
use \Symbb\Core\SystemBundle\Manager\ConfigManager;
use Symbb\Core\UserBundle\Entity\UserInterface;
use Symbb\Core\UserBundle\Manager\UserManager;

class TopicManager extends \Symbb\Core\SystemBundle\Manager\AbstractManager
{

    /**
     *
     * @var ConfigManager
     */
    protected $configManager;

    /**
     *
     * @var TopicFlagHandler
     */
    protected $topicFlagHandler;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * TopicManager constructor.
     * @param TopicFlagHandler $topicFlagHandler
     * @param ConfigManager $configManager
     * @param UserManager $userManager
     */
    public function __construct(
        TopicFlagHandler $topicFlagHandler,
        ConfigManager $configManager,
        UserManager $userManager
    )
    {
        $this->topicFlagHandler = $topicFlagHandler;
        $this->configManager = $configManager;
        $this->userManager = $userManager;
    }

    /**
     * @param Topic $topic
     * @return UserInterface
     */
    public function getAuthor(Topic $topic){
        $authorId = $topic->getAuthorId();
        return $this->userManager->find($authorId);
    }

    /**
     *
     * @param int $topicId
     * @return \Symbb\Core\ForumBundle\Entity\Topic
     */
    public function find($topicId)
    {
        $post = $this->em->getRepository('SymbbCoreForumBundle:Topic')->find($topicId);
        return $post;
    }

    /**
     *
     * @param int $topicId
     * @return Post[]
     */
    public function findPosts(\Symbb\Core\ForumBundle\Entity\Topic $topic, $page = 1, $limit = null, $orderDir = 'desc')
    {
        $cacheKey = implode("_", array("findPosts", $topic->getId(), $page, $limit, $orderDir));
        $pagination = $this->getCacheData($cacheKey);
        if ($pagination === null) {
            if ($limit === null) {
                $limit = $topic->getForum()->getEntriesPerPage();
            }

            $sql = "SELECT
                    p
                FROM
                    SymbbCoreForumBundle:Post p
                WHERE
                  p.topic = ?1
                ORDER BY
                  p.created " . strtoupper($orderDir);

            $query = $this->em->createQuery($sql);
            $query->setParameter(1, $topic->getId());

            $pagination = $this->createPagination($query, $page, $limit);
            $this->setCacheData($cacheKey, $pagination);
        }

        return $pagination;
    }

    /**
     * @param Topic $topic
     * @return Post
     */
    public function getLastPost(Topic $topic)
    {
        $posts = $this->findPosts($topic, 1, 1, "desc");
        return $posts->current();
    }

    /**
     *
     * @return \Symbb\Core\ForumBundle\DependencyInjection\TopicFlagHandler
     */
    public function getFlagHandler()
    {
        return $this->topicFlagHandler;
    }

    public function getBreadcrumbData(\Symbb\Core\ForumBundle\Entity\Topic $object, ForumManager $forumManager)
    {
        $breadcrumb = array();
        $forum = $object->getForum();
        if (\is_object($forum) && $forum->getId() > 0) {
            $breadcrumb = $forumManager->getBreadcrumbData($forum);
            $breadcrumb[] = array(
                'type' => 'topic',
                'name' => $object->getName(),
                'seoName' => $object->getSeoName(),
                'id' => $object->getId()
            );
        }
        return $breadcrumb;
    }

    /**
     * @param Topic $topic
     * @return bool
     */
    public function markAsRead(Topic $topic)
    {
        $this->topicFlagHandler->removeFlag($topic, AbstractFlagHandler::FLAG_NEW);
        return true;
    }

    /**
     * @param Topic $topic
     * @param string $searchForFlag
     * @return bool
     */
    public function checkFlag(Topic $topic, $searchForFlag = '')
    {
        if(empty($searchForFlag)){
            $searchForFlag = AbstractFlagHandler::FLAG_NEW;
        }
        foreach ($this->topicFlagHandler->findAll($topic) as $flag) {
            if ($flag->getFlag() == $searchForFlag) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Topic $topic
     * @param string $flag
     * @return bool
     */
    public function hasFlag(Topic $topic, $flag = null)
    {
        if(!$flag){
            $flag = AbstractFlagHandler::FLAG_NEW;
        }
        return $this->checkFlag($topic, $flag);
    }

    /**
     * @param Topic $topic
     * @return bool
     */
    public function save(Topic $topic)
    {
        $this->em->persist($topic);
        $this->em->persist($topic->getMainPost());
        $this->em->flush();
        return true;
    }


    public function getParticipatedTopics($page = 1, $limit = 20, UserInterface $user = null)
    {

        $sql = "SELECT
                    t
                FROM
                    SymbbCoreForumBundle:Topic t
                JOIN
                    t.posts p
                WHERE
                    p.authorId = ?0
                GROUP BY
                    t.id
                ORDER BY
                    p.created DESC ";

        if (!$user) {
            $user = $this->getUser();
        }

        $query = $this->em->createQuery($sql);
        $query->setParameter(0, $user->getId());

        $pagination = $this->createPagination($query, $page, $limit);

        return $pagination;
    }

    public function findBy($criteria, $page = 1, $limit = 20){

        $where = "WHERE ";
        $values = array();
        $valueKey = 0;
        foreach($criteria as $field => $crit){
            $where .= "t.".$field;
            if(is_array($crit)){
                if(!isset($crit["operator"]) || !isset($crit["value"])){
                    throw new \Exception("Criteria array need to have a operator and value element");
                }
                if($crit["operator"] == "IN"){
                    $where .= " IN (?".$valueKey.")";
                } else {
                    $where .= " ".$crit["operator"]." ?".$valueKey;
                }
                $values[$valueKey] = $crit["value"];
            } else {
                $where .= " = ?".$valueKey;
                $values[$valueKey] = $crit;
            }
            $valueKey++;
        }

        $sql = "SELECT
                    t
                FROM
                    SymbbCoreForumBundle:Topic t
                ".$where."
                ORDER BY
                    t.id DESC ";


        $query = $this->em->createQuery($sql);
        foreach($values as $key => $value){
            $query->setParameter($key, $value);
        }

        $pagination = $this->createPagination($query, $page, $limit);

        return $pagination;
    }

    /**
     * @param Topic $topic
     * @param Forum $forum
     * @return bool
     */
    public function move(Topic $topic, Forum $forum)
    {
        $topic->setForum($forum);
        $this->em->persist($topic);
        $this->em->flush();

        return true;
    }

    /**
     * @param Topic $topic
     * @return bool
     */
    public function delete(Topic $topic)
    {
        $this->em->remove($topic);
        $this->em->flush();

        return true;
    }

    /**
     * @param Topic $topic
     */
    public function close(Topic $topic)
    {
        $topic->setLocked(true);
        $this->em->persist($topic);
        $this->em->flush();

        return true;
    }

    /**
     * @param Topic $topic
     * @return bool
     */
    public function open(Topic $topic)
    {
        $topic->setLocked(false);
        $this->em->persist($topic);
        $this->em->flush();

        return true;
    }

    public function getAvailableTags()
    {
        $sql = "SELECT
                    tag
                FROM
                    SymbbCoreForumBundle:Topic\Tag tag
                ORDER BY
                    tag.priority DESC ";
        $query = $this->em->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @param Topic $topic
     * @param UserInterface $user
     */
    public function subscribeNotification(Topic $topic, UserInterface $user = null){
        $this->topicFlagHandler->insertFlag($topic, TopicFlagHandler::FLAG_NOTIFY, $user);
    }

    /**
     * @param Topic $topic
     * @param UserInterface $user
     */
    public function unsubscribeNotification(Topic $topic, UserInterface $user = null){
        $this->topicFlagHandler->removeFlag($topic, TopicFlagHandler::FLAG_NOTIFY, $user);
    }
}
