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
    public function findTopics(Forum $forum, $page = 1, $limit = null, $orderDir = 'desc', $flags = array())
    {
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
                  tag.priority DESC, p.created DESC";

        $query = $this->em->createQuery($sql);
        $query->setParameter(1, $forum->getId());


        $pagination = $this->createPagination($query, $page, $limit);

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
     * @param null $offset
     * @return array(<\Symbb\Core\ForumBundle\Entity\Forum>)
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
                    SymbbCoreForumBundle:Forum f
                WHERE
                    ".$parentWhere."
                    (
                        ( SELECT COUNT(a.id) FROM SymbbCoreSystemBundle:Access a WHERE
                            a.objectId = f.id AND
                            a.object = 'Symbb\Core\ForumBundle\Entity\Forum' AND
                            a.identity = '".$userlcass."' AND
                            a.identityId = ?1 AND
                            a.access = 'view'
                            ORDER BY a.id
                        ) > 0 OR
                        ( SELECT COUNT(a2.id) FROM SymbbCoreSystemBundle:Access a2 WHERE
                            a2.objectId = f.id AND
                            a2.object = 'Symbb\Core\ForumBundle\Entity\Forum' AND
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
            if($checkAccess){
                $this->accessManager->addAccessCheck(ForumVoter::VIEW, $entity);
                $access = $this->accessManager->hasAccess();
            }
            if($access){
                if(in_array($entity->getType(), $types) || empty($types)){
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
            $name = $parent->getName(). ' > ' . $name;
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
    public function ignoreForum(\Symbb\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
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
    public function watchForum(\Symbb\Core\ForumBundle\Entity\Forum $forum, ForumFlagHandler $flagHandler)
    {
        $flagHandler->removeFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach ($subForms as $subForm) {
            $this->watchForum($subForm, $flagHandler);
        }
        return true;
    }

    /**
     * @return bool
     */
    public function markAllAsRead(){
        $forums = $this->findAll(null, 999 , 1);
        foreach($forums as $forum){
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

        $this->forumFlagHandler->removeFlag($forum, 'new');

        $topics = $forum->getTopics();
        foreach ($topics as $topic) {
            $this->topicFlagHandler->removeFlag($topic, 'new');
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
    public function update(Forum $object){
        $this->em->persist($object);
        $this->em->flush();
        return true;
    }

    /**
     * @param Forum $object
     * @return bool
     */
    public function remove(Forum $object){
        $this->em->remove($object);
        $this->em->flush();
        return true;
    }
}