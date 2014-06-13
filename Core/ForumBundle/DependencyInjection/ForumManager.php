<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\DependencyInjection;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use \Doctrine\ORM\Query\Lexer;
use Symfony\Component\Security\Core\Util\ClassUtils;
use \SymBB\Core\ForumBundle\Entity\Forum;
use SymBB\Core\ForumBundle\Entity\Post;
use SymBB\Core\SystemBundle\DependencyInjection\AbstractManager;
use \SymBB\Core\SystemBundle\DependencyInjection\ConfigManager;

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

    public function __construct(
        TopicFlagHandler $topicFlagHandler, PostFlagHandler $postFlagHandler, ConfigManager $configManager
    )
    {
        $this->topicFlagHandler = $topicFlagHandler;
        $this->postFlagHandler = $postFlagHandler;
        $this->configManager = $configManager;
    }

    public function findNewestTopics(Forum $parent = null)
    {
        $topics = $this->topicFlagHandler->findTopicsByFlag('new', $parent);
        return $topics;
    }

    /**
     *
     * @param \SymBB\Core\ForumBundle\Entity\Forum $forum
     * @param int page
     * @param int $limit
     * @param string $orderDir
     * @return array
     */
    public function findTopics(Forum $forum, $page = 1, $limit = null, $orderDir = 'desc')
    {
        if ($limit === null) {
            $limit = $forum->getEntriesPerPage();
        }

        $qbPage = $this->em->createQueryBuilder();
        $qbPage->select('count(t)')
            ->from('SymBBCoreForumBundle:Topic', 't')
            ->where('t.forum = ?1')
            ->orderby('t.created', $orderDir)
            ->setParameter(1, $forum->getId());
        $queryPage = $qbPage->getQuery();
        $count = $queryPage->getSingleScalarResult();

        $query = $this->em->createQuery('
                SELECT t FROM SymBBCoreForumBundle:Topic t
                LEFT JOIN t.tags tag
                WHERE t.forum = ?1
                GROUP BY t.id
                ORDER BY tag.priority DESC, t.created '.$orderDir.'
                '
        )
            ->setParameter(1, $forum->getId());
        $query->setHint('knp_paginator.count', $count);

        if ($page === 'last') {
            $page = \ceil($count / $limit);
        }

        $pagination = $this->paginator->paginate(
            $query, $page, $limit, array('distinct' => false)
        );

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

        $qb = $this->em->getRepository('SymBBCoreForumBundle:Post')->createQueryBuilder('p');
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
        $childs = $parent->getChildren();
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
        $qb = $this->em->getRepository('SymBBCoreForumBundle:Post')->createQueryBuilder('p');
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
        $qb = $this->em->getRepository('SymBBCoreForumBundle:Post')->createQueryBuilder('p');
        $qb->select('COUNT(p.id)');
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * @param null $parentId
     * @param null $limit
     * @param null $offset
     * @return array(<\SymBB\Core\ForumBundle\Entity\Forum>)
     */
    public function findAll($parentId = null, $limit = null, $page = null)
    {
        if ($parentId === 0) {
            $parentId = null;
        }

        if(!$page){
            $page = 1;
        }

        if(!$limit){
            $limit = 999;
        }


        $parentWhere = 'f.parent = ?0 AND';
        if(!$parentId){
            $parentWhere = 'f.parent IS NULL AND';
        }


        $configUsermanager  = $this->configManager->getSymbbConfig('usermanager');
        $configGroupManager = $this->configManager->getSymbbConfig('groupmanager');

        $userlcass = $configUsermanager['user_class'];
        $groupclass = $configGroupManager['group_class'];

        $sql = "SELECT
                    f
                FROM
                    SymBBCoreForumBundle:Forum f
                WHERE
                    ".$parentWhere."
                    (
                        ( SELECT COUNT(a.id) FROM SymBBCoreSystemBundle:Access a WHERE
                            a.objectId = f.id AND
                            a.object = 'SymBB\Core\ForumBundle\Entity\Forum' AND
                            a.identity = '".$userlcass."' AND
                            a.identityId = ?1 AND
                            a.access = 'view'
                            ORDER BY a.id
                        ) > 0 OR
                        ( SELECT COUNT(a2.id) FROM SymBBCoreSystemBundle:Access a2 WHERE
                            a2.objectId = f.id AND
                            a2.object = 'SymBB\Core\ForumBundle\Entity\Forum' AND
                            a2.identity = '".$groupclass."' AND
                            a2.identityId IN (?2) AND
                            a2.access = 'view'
                            ORDER BY a2.id
                        ) > 0
                    )
                ORDER BY
                    f.position ASC";

        $groupIds = array();
        foreach($this->getUser()->getGroups() as $group){
            $groupIds[]  = $group->getId();
        }

        $query = $this->em->createQuery($sql);
        if($parentId){
            $query->setParameter(0, $parentId);
        }
        $query->setParameter(1, $this->getUser()->getId());
        $query->setParameter(2, $groupIds);

        $pagination = $this->createPagination($query, $page, $limit);

        return $pagination;
    }

    /**
     *
     * @param int $forumId
     * @return \SymBB\Core\ForumBundle\Entity\Forum
     */
    public function find($forumId)
    {
        return $this->em->getRepository('SymBBCoreForumBundle:Forum')->find($forumId);
    }

    /**
     * @param array $types
     * @return array
     */
    public function getSelectList($types = array())
    {
        $repo = $this->em->getRepository('SymBBCoreForumBundle:Forum');
        $list = array();
        $by = array('parent' => null);
        if (!empty($types)) {
            $by['type'] = $types;
        }
        $entries = $repo->findBy($by, array('position' => 'ASC', 'name' => 'ASC'));
        foreach ($entries as $entity) {
            $list[$entity->getId()] = $entity;
            $this->addChildsToArray($entity, $list);
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
            $name = '─' . $name;
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
        $childs = $entity->getChildren();
        if (!empty($childs) && count($childs) > 0) {
            foreach ($childs as $child) {
                $array[$child->getId()] = $child;
                $this->addChildsToArray($child, $array);
            }
        }
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
     * @param ForumFlagHandler $flagHandler
     * @return bool
     */
    public function isIgnored(\SymBB\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
    {
        $check = $flagHandler->checkFlag($forum, 'ignore');
        return $check;
    }

    /**
     * @param Forum $forum
     * @param ForumFlagHandler $flagHandler
     * @return bool
     */
    public function ignoreForum(\SymBB\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
    {
        $flagHandler->insertFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->ignoreForum($subForm, $flagHandler);
        }
        return true;
    }

    /**
     * @param Forum $forum
     * @param ForumFlagHandler $flagHandler
     * @return bool
     */
    public function watchForum(\SymBB\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
    {
        $flagHandler->removeFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->watchForum($subForm, $flagHandler);
        }
        return true;
    }

    /**
     * @param Forum $forum
     * @param ForumFlagHandler $flagHandler
     * @return bool
     */
    public function markAsRead(\SymBB\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
    {

        $flagHandler->removeFlag($forum, 'new');

        $topics = $forum->getTopics();
        foreach ($topics as $topic) {
            $this->topicFlagHandler->removeFlag($topic, 'new');
        }

        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->markAsRead($subForm, $flagHandler);
        }

        return true;
    }
}