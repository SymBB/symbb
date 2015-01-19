<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\DependencyInjection;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use \Doctrine\ORM\Query\Lexer;
use Symbb\Core\ForumBundle\Security\Authorization\ForumVoter;
use Symbb\Core\SystemBundle\Manager\AbstractFlagHandler;
use Symbb\Core\UserBundle\Entity\GroupInterface;
use Symbb\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;
use \Symbb\Core\ForumBundle\Entity\Forum;
use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\SystemBundle\Manager\AbstractManager;
use \Symbb\Core\SystemBundle\Manager\ConfigManager;

class ForumManager extends AbstractManager
{

    /**
     *
     * @var TopicFlagHandler
     */
    protected $topicFlagHandler;

    /**
     *
     * @var PostFlagHandler
     */
    protected $postFlagHandler;

    /**
     *
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var ForumFlagHandler
     */
    protected $forumFlagHandler;

    public function __construct(
        TopicFlagHandler $topicFlagHandler, PostFlagHandler $postFlagHandler, ConfigManager $configManager, ForumFlagHandler $forumFlagHandler
    )
    {
        $this->topicFlagHandler = $topicFlagHandler;
        $this->postFlagHandler = $postFlagHandler;
        $this->configManager = $configManager;
        $this->forumFlagHandler = $forumFlagHandler;
    }

    /**
     *
     * @param \Symbb\Core\ForumBundle\Entity\Forum $forum
     * @param int page
     * @param int $limit
     * @param string $orderDir
     * @return array
     */
    public function findTopics(Forum $forum, $page = 1, $limit = null, $orderDir = 'desc')
    {

        $cackeKey = implode("_", array("findTopics", $forum->getId(), $page, $limit, $orderDir));
        $pagination = $this->getCacheData($cackeKey);

        if ($pagination === null) {
            if ($limit === null) {
                $limit = $forum->getEntriesPerPage();
            }

            $sql = "SELECT
                    t
                FROM
                    SymbbCoreForumBundle:Topic t
                LEFT JOIN
                    t.tags tag
                LEFT JOIN
                    t.posts p
                WHERE
                  t.forum = ?1 AND
                  p.id = (SELECT MAX(p2.id) FROM SymbbCoreForumBundle:POST p2 WHERE p2.topic = t.id ORDER BY p2.created )
                GROUP BY
                  t.id
                ORDER BY
                  tag.priority " . strtoupper($orderDir) . ", p.created " . strtoupper($orderDir);

            $query = $this->em->createQuery($sql);
            $query->setParameter(1, $forum->getId());

            $pagination = $this->createPagination($query, $page, $limit);
            $this->setCacheData($cackeKey, $pagination);
        }

        return $pagination;
    }

    /**
     * @param Forum $parent
     * @param null $limit
     * @param int $pageNumber
     * @return mixed
     */
    public function findPosts(Forum $parent = null, $limit = null, $pageNumber = 1)
    {
        if ($limit === null) {
            $limit = $this->configManager->get('newpost.max', "forum");
        }

        $childIds = array();

        if ($parent) {
            $childIds = $this->getChildIds($parent);
        }

        $qb = $this->em->getRepository('SymbbCoreForumBundle:Post')->createQueryBuilder('p');
        $qb->select("p");
        if (!empty($childIds)) {
            $qb->join('p.topic', 't');
            $qb->where("t.forum IN ( :forums )");
        }
        $qb->orderBy("p.created", "DESC");
        $query = $qb->getQuery();
        if (!empty($childIds)) {
            $query->setParameter('forums', $childIds);
        }
        $paginator = $this->paginator;
        $pagination = $paginator->paginate(
            $query, $pageNumber/* page number */, $limit/* limit per page */
        );

        return $pagination;
    }

    /**
     * @param Forum $parent
     * @param array $childIds
     * @return array
     */
    public function getChildIds(Forum $parent, $childIds = array())
    {
        $childs = $this->getChildren($parent);
        $childIds[] = $parent->getId();
        foreach ($childs as $child) {
            $childIds[] = $child->getId();
            $childIds = $this->getChildIds($child, $childIds);
        }
        return $childIds;
    }

    /**
     * @todo join forum and check active
     * @return int
     */
    public function countTopics()
    {
        $qb = $this->em->getRepository('SymbbCoreForumBundle:Post')->createQueryBuilder('p');
        $qb->select('COUNT(p.id)');
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * @todo join forum and check active
     * @return int
     */
    public function countPosts()
    {
        $qb = $this->em->getRepository('SymbbCoreForumBundle:Post')->createQueryBuilder('p');
        $qb->select('COUNT(p.id)');
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * @param null $parentId
     * @param null $limit
     * @param null $page
     * @param bool $checkAccess
     * @return Forum[]
     */
    public function findAll($parentId = null, $limit = null, $page = null, $checkAccess = true)
    {
        if ($parentId === 0) {
            $parentId = null;
        }

        $parentWhere = 'WHERE f.parent = ?0';
        if (!$parentId) {
            $parentWhere = 'WHERE f.parent IS NULL';
        }

        $accessWhere = "";

        if ($checkAccess) {
            $accessWhere = "
                JOIN
                    SymbbCoreSystemBundle:Access a
                " . $parentWhere . " AND
                    a.object = ?1 AND
                    a.objectId = f.id AND
                    a.access = ?2 AND
                    (
                        (
                          a.identity = ?3 AND
                          a.identityId = ?4
                        ) OR
                        (
                          a.identity = ?5 AND
                          a.identityId IN (?6)
                        )
                    )
                ";
        }

        $sql = "SELECT
                    f
                FROM
                    SymbbCoreForumBundle:Forum f
                " . $accessWhere . "
                ORDER BY
                    f.position ASC";

        $groupIds = array();
        foreach ($this->getUser()->getGroups() as $group) {
            $groupIds[] = $group->getId();
        }

        $query = $this->em->createQuery($sql);

        if ($parentId) {
            $query->setParameter(0, $parentId);
        }

        if ($checkAccess) {

            $groupClass = "";
            $groupIds = array();
            foreach ($this->getUser()->getGroups() as $group) {
                $groupClass = get_class($group);
                $groupIds[] = $group->getId();
            }

            $query->setParameter(1, "Symbb\Core\ForumBundle\Entity\Forum");
            $query->setParameter(2, ForumVoter::VIEW);
            $query->setParameter(3, get_class($this->getUser()));
            $query->setParameter(4, $this->getUser()->getId());
            $query->setParameter(5, $groupClass);
            $query->setParameter(6, $groupIds);
        }

        $pagination = $this->createPagination($query, $page, $limit);

        return $pagination;
    }

    /**
     *
     * @param int $forumId
     * @return \Symbb\Core\ForumBundle\Entity\Forum
     */
    public function find($forumId)
    {
        return $this->em->getRepository('SymbbCoreForumBundle:Forum')->find($forumId);
    }

    /**
     * @param array $types
     * @return array
     */
    public function getSelectList($types = array(), $checkAccess = true)
    {

        $types = (array)$types;

        $repo = $this->em->getRepository('SymbbCoreForumBundle:Forum');
        $list = array();
        $by = array('parent' => null);
        $entries = $repo->findBy($by, array('position' => 'ASC', 'name' => 'ASC'));
        foreach ($entries as $entity) {
            $access = true;
            if ($checkAccess) {
                $access = $this->userManager->isGranted(ForumVoter::VIEW, $entity);
            }
            if ($access) {
                if (in_array($entity->getType(), $types) || empty($types)) {
                    $list[$entity->getId()] = $entity;
                }
                $this->addChildsToArray($entity, $list);
            }
        }

        $listFinal = array();

        foreach ($list as $forum) {
            $name = ' ' . $forum->getName();
            $name = $this->addSpaceForParents($forum, $name);
            $listFinal[$forum->getId()] = $name;
        }

        return $listFinal;
    }

    /**
     * @param $forum
     * @param $name
     * @return string
     */
    private function addSpaceForParents($forum, $name)
    {
        $parent = $forum->getParent();
        if (is_object($parent)) {
            $name = $parent->getName() . ' > ' . $name;
            $name = $this->addSpaceForParents($parent, $name);
        }
        return $name;
    }

    /**
     * @param $entity
     * @param $array
     */
    private function addChildsToArray($entity, &$array)
    {
        $childs = $this->getChildren($entity);
        if (!empty($childs) && count($childs) > 0) {
            foreach ($childs as $child) {
                $array[$child->getId()] = $child;
                $this->addChildsToArray($child, $array);
            }
        }
    }

    /**
     * @param Forum $forum
     * @return int
     */
    public function getTopicCount(Forum $forum)
    {

        $cacheKey = implode("_", array("getTopicCount", $forum->getId()));
        $count = $this->getCacheData($cacheKey);

        if ($count === null) {

            $ids = array($forum->getId());
            $this->getAllForumChildIds($forum, $ids);

            $sql = "SELECT COUNT(t.id) FROM SymbbCoreForumBundle:Topic t WHERE t.forum IN (?0)";
            $query = $this->em->createQuery($sql);
            $query->setParameter(0, $ids);
            $count = $query->getSingleScalarResult();
            if (!$count) {
                $count = 0;
            }
            $this->setCacheData($cacheKey, $count);
        }

        return $count;
    }

    /**
     * @param Forum $forum
     * @return int|mixed
     */
    public function getPostCount(Forum $forum)
    {

        $cacheKey = implode("_", array("getPostCount", $forum->getId()));
        $count = $this->getCacheData($cacheKey);

        if ($count === null) {

            $ids = array($forum->getId());
            $this->getAllForumChildIds($forum, $ids);

            $sql = "SELECT COUNT(p.id) FROM SymbbCoreForumBundle:Post p JOIN p.topic t WHERE t.forum IN (?0)";
            $query = $this->em->createQuery($sql);
            $query->setParameter(0, $ids);
            $count = $query->getSingleScalarResult();
            if (!$count) {
                $count = 0;
            }
            $this->setCacheData($cacheKey, $count);
        }

        return $count;
    }

    /**
     * @param Forum $forum
     * @param int $page
     * @param int $limit
     * @return Forum[]
     */
    public function getChildren(Forum $forum, $page = 1, $limit = 20, $checkAccess = true)
    {


        $cacheKey = implode("_", array("getPostCount", $forum->getId(), $page, $limit, $checkAccess));
        $pagination = $this->getCacheData($cacheKey);

        if ($pagination === null) {

            $i = 0;
            $wherePart = " WHERE f.parent IS NULL ";
            if ($forum->getId() > 0) {
                $wherePart = " WHERE f.parent = ?0 ";
            }

            if ($checkAccess) {
                $wherePart = "
            JOIN
                SymbbCoreSystemBundle:Access a
            " . $wherePart . " AND
                a.object = ?1 AND
                a.objectId = f.id AND
                a.access = ?2 AND
                (
                    (
                      a.identity = ?3 AND
                      a.identityId = ?4
                    ) OR
                    (
                      a.identity = ?5 AND
                      a.identityId IN (?6)
                    )
                    )
            ";
            }

            $sql = "SELECT
                f
            FROM
                SymbbCoreForumBundle:Forum f
            " . $wherePart . "
            ORDER BY
                f.position ASC";

            $groupClass = "";
            $groupIds = array();
            foreach ($this->getUser()->getGroups() as $group) {
                $groupClass = get_class($group);
                $groupIds[] = $group->getId();
            }

            $query = $this->em->createQuery($sql);
            if ($forum->getId() > 0) {
                $query->setParameter(0, $forum->getId());
            }
            $query->setParameter(1, get_class($forum));
            $query->setParameter(2, ForumVoter::VIEW);
            $query->setParameter(3, get_class($this->getUser()));
            $query->setParameter(4, $this->getUser()->getId());
            $query->setParameter(5, $groupClass);
            $query->setParameter(6, $groupIds);

            $pagination = $this->createPagination($query, $page, $limit);
            $this->setCacheData($cacheKey, $pagination);
        }

        return $pagination;
    }

    /**
     * @param $object
     * @return array
     */
    public function getBreadcrumbData($object = null)
    {
        $breadcrumb = array();

        while (is_object($object) && $object->getId() > 0) {
            $breadcrumb[] = array(
                'type' => 'forum',
                'name' => $object->getName(),
                'seoName' => $object->getSeoName(),
                'id' => $object->getId()
            );
            $object = $object->getParent();
        };
        $home = $this->translator->trans('Home', array(), 'symbb_frontend');
        $breadcrumb[] = array('name' => $home, 'type' => 'home');
        $breadcrumb = array_reverse($breadcrumb);

        return $breadcrumb;
    }

    /**
     * @param Forum $forum
     * @return bool
     */
    public function isIgnored(\Symbb\Core\ForumBundle\Entity\Forum $forum)
    {
        $check = $this->forumFlagHandler->checkFlag($forum, 'ignore');
        return $check;
    }

    /**
     * @param Forum $forum
     * @param ForumFlagHandler $flagHandler
     * @return bool
     */
    public function ignoreForum(\Symbb\Core\ForumBundle\Entity\Forum $forum)
    {
        $this->forumFlagHandler->insertFlag($forum, 'ignore');
        $subForms = $this->getChildren($forum);
        foreach ($subForms as $subForm) {
            $this->ignoreForum($subForm);
        }
        return true;
    }

    /**
     * @param Forum $forum
     * @param ForumFlagHandler $flagHandler
     * @return bool
     */
    public function watchForum(Forum $forum)
    {
        $this->forumFlagHandler->removeFlag($forum, 'ignore');
        $subForms = $this->getChildren($forum);
        foreach ($subForms as $subForm) {
            $this->watchForum($subForm);
        }
        return true;
    }

    /**
     * @param Forum $forum
     * @return bool
     */
    public function unignoreForum(Forum $forum)
    {
        return $this->watchForum($forum);
    }

    /**
     * @return bool
     */
    public function markAllAsRead()
    {
        $forums = $this->findAll(null, 999, 1);
        foreach ($forums as $forum) {
            $this->markAsRead($forum);
        }
        return true;
    }

    /**
     * @param Forum $forum
     * @param ForumFlagHandler $flagHandler
     * @return bool
     */
    public function markAsRead(\Symbb\Core\ForumBundle\Entity\Forum $forum)
    {

        $this->forumFlagHandler->removeFlag($forum, AbstractFlagHandler::FLAG_NEW);

        $topics = $forum->getTopics();
        foreach ($topics as $topic) {
            $this->topicFlagHandler->removeFlag($topic, AbstractFlagHandler::FLAG_NEW);
        }

        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->markAsRead($subForm);
        }

        return true;
    }

    /**
     * @param Forum $object
     * @return bool
     */
    public function update(Forum $object)
    {
        $this->em->persist($object);
        $this->em->flush();
        return true;
    }

    /**
     * @param Forum $object
     * @return bool
     */
    public function remove(Forum $object)
    {
        $this->em->remove($object);
        $this->em->flush();
        return true;
    }


    /**
     * @param Forum $forumFrom
     * @param Forum $forumTo
     * @param GroupInterface $group
     * @param bool $includeChilds
     */
    public function copyAccessOfGroup(Forum $forumFrom, Forum $forumTo, GroupInterface $group, $includeChilds = false)
    {
        $this->accessManager->copyAccessForIdentity($forumFrom, $forumTo, $group);
        if ($includeChilds) {
            foreach ($this->getChildren($forumTo) as $child) {
                $this->copyAccessOfGroup($forumFrom, $child, $group, $includeChilds);
            }
        }
    }

    /**
     * @param Forum $forum
     * @param GroupInterface $group
     * @param $accessSet
     * @param bool $includeChilds
     */
    public function applyAccessSetForGroup(Forum $forum, GroupInterface $group, $accessSet, $includeChilds = false)
    {
        $this->accessManager->applyAccessSetForIdentity($forum, $group, $accessSet);
        if ($includeChilds) {
            foreach ($this->getChildren($forum) as $child) {
                $this->applyAccessSetForGroup($child, $group, $accessSet, $includeChilds);
            }
        }
    }

    /**
     * @param Forum $forum
     * @param $flag
     * @param UserInterface $user
     * @return bool
     */
    public function hasFlag(Forum $forum, $flag, UserInterface $user = null)
    {
        return $this->forumFlagHandler->checkFlag($forum, $flag, $user = null);
    }

    /**
     * @param Forum $forum
     * @return Post
     */
    public function getLastPost(Forum $forum)
    {

        $ids = array($forum->getId());
        $this->getAllForumChildIds($forum, $ids);

        $sql = "SELECT
                    p
                FROM
                    SymbbCoreForumBundle:Post p
                JOIN
                    p.topic t
                WHERE
                    t.forum IN (?0)
                ORDER BY
                    p.created DESC";

        $query = $this->em->createQuery($sql);
        $query->setParameter(0, $ids);
        $query->setMaxResults(1);

        $post = $query->getOneOrNullResult();
        return $post;
    }

    /**
     * @param $forum
     * @param $ids
     */
    public function getAllForumChildIds($forum, &$ids){
        //todo cache this will not change often, refesh cache at backend forum save action!
        foreach($this->getChildren($forum, 1, 100) as $child){
            $id = $child->getId();
            $ids[$id] = $id;
            $this->getAllForumChildIds($child, $ids);
        }
    }
}