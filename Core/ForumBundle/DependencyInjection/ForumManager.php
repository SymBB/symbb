<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\DependencyInjection;

use \SymBB\Core\ForumBundle\Entity\Forum;
use \Symfony\Component\Security\Core\SecurityContextInterface;
use SymBB\Core\ForumBundle\DependencyInjection\TopicFlagHandler;
use SymBB\Core\ForumBundle\DependencyInjection\PostFlagHandler;
use \SymBB\Core\SystemBundle\DependencyInjection\ConfigManager;

class ForumManager extends \SymBB\Core\SystemBundle\DependencyInjection\AbstractManager
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
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    
    protected $paginator;
    
    protected $translator;

    public function __construct(
    SecurityContextInterface $securityContext, TopicFlagHandler $topicFlagHandler, PostFlagHandler $postFlagHandler, ConfigManager $configManager, $em, $paginator, $translator
    )
    {
        $this->securityContext = $securityContext;
        $this->topicFlagHandler = $topicFlagHandler;
        $this->postFlagHandler = $postFlagHandler;
        $this->configManager = $configManager;
        $this->em = $em;
        $this->paginator = $paginator;
        $this->translator = $translator;
    }

    public function findNewestTopics(Forum $parent = null)
    {
        $topics = $this->topicFlagHandler->findTopicsByFlag('new', $parent);
        return $topics;
    }

    public function findNewestPosts(Forum $parent = null, $limit = null, $pageNumber = 1)
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
        $qb->join('p.topic', 't');
        $qb->join('t.flags', 'f', \Doctrine\ORM\Query\Expr\Join::WITH, 'f.flag = :flag AND f.user = :user');
        if (!empty($childIds)) {
            $qb->where("t.forum IN ( :forums )");
        }
        $qb->orderBy("p.created", "DESC");
        $query = $qb->getQuery();
        $query->setParameter('flag', "new");
        $query->setParameter('user', $this->getUser()->getId());
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
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Forum $forum
     * @param type $limit
     * @param type $offset
     * @param type $orderDir
     * @return array
     */
    public function findTopics(Forum $forum, $limit = null, $offset = 0, $orderDir = 'desc')
    {
        if($limit === null){
            $limit = $forum->getEntriesPerPage();
        }
        
        $topics = $this->em->getRepository('SymBBCoreForumBundle:Topic')->findBy(array('forum' => $forum->getId()), array('created' => $orderDir), $limit, $offset);
        
        return $topics;
    }
    
    
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
     * 
     * @return array(<\SymBB\Core\ForumBundle\Entity\Forum>)
     */
    public function findAll($parentId = null, $limit = null, $offset = null)
    {
        if ($parentId === 0) {
            $parentId = null;
        }

        $forumList = $this->em->getRepository('SymBBCoreForumBundle:Forum')->findBy(array('active' => 1, 'parent' => $parentId), array(), $limit, $offset);

        return $forumList;
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

    private function addSpaceForParents($forum, $name)
    {
        $parent = $forum->getParent();
        if (is_object($parent)) {
            $name = '─' . $name;
            $name = $this->addSpaceForParents($parent, $name);
        }
        return $name;
    }

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
    
    public function getBreadcrumbData($object){
        $breadcrumb = array();
        
        while (is_object($object)) {
            $breadcrumb[] = array(
                'type' => 'forum',
                'name' => $object->getName(),
                'id' => $object->getId()
            );
            $object = $object->getParent();
        };
        $home = $this->translator->trans('Home', array(), 'symbb_frontend');
        $breadcrumb[] = array('name' => $home, 'type' => 'home');
        $breadcrumb = array_reverse($breadcrumb);
        
        return $breadcrumb;
    }
    
    public function isIgnored(\SymBB\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
    {
        $check = $flagHandler->checkFlag($forum, 'ignore');
        return $check;
    }
    
    public function ignoreForum(\SymBB\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
    {
        $flagHandler->insertFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->ignoreForum($subForm, $flagHandler);
        }
        return true;
    }

    public function watchForum(\SymBB\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
    {
        $flagHandler->removeFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->watchForum($subForm, $flagHandler);
        }
        return true;
    }
    
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
