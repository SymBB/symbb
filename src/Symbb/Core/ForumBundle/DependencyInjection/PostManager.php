<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\DependencyInjection;

use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\ForumBundle\Event\PostManagerParseTextEvent;
use Symbb\Core\SystemBundle\Manager\AbstractManager;
use \Symbb\Core\SystemBundle\Manager\ConfigManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use \Doctrine\ORM\Query\Lexer;
use Symfony\Component\Security\Core\Util\ClassUtils;

class PostManager extends AbstractManager
{

    /**
     *
     * @var ConfigManager
     */
    protected $configManager;

    /**
     *
     * @var PostFlagHandler
     */
    protected $postFlagHandler;

    public function __construct(
        PostFlagHandler $postFlagHandler, ConfigManager $configManager
    )
    {
        $this->postFlagHandler = $postFlagHandler;
        $this->configManager = $configManager;
    }

    public function parseText(Post $post)
    {
        $text = $post->getText();
        $event = new PostManagerParseTextEvent($post, (string)$text);
        $this->eventDispatcher->dispatch('symbb.core.forum.post.manager.parse.text', $event);
        $text = $event->getText();

        return $text;
    }

    public function cleanText(Post $post)
    {
        $text = $post->getText();
        $event = new PostManagerParseTextEvent($post, $text);
        $this->eventDispatcher->dispatch('symbb.core.forum.post.manager.clean.text', $event);
        $text = $event->getText();
        return $text;
    }

    /**
     *
     * @param int $postId
     * @return \Symbb\Core\ForumBundle\Entity\Post
     */
    public function find($postId)
    {
        $post = $this->em->getRepository('SymbbCoreForumBundle:Post')->find($postId);
        return $post;
    }

    /**
     *
     * @param int $topicId
     * @return array(<\Symbb\Core\ForumBundle\Entity\Post>)
     */
    public function findByTopic(Topic $topic, $limit = null, $pageNumber = 1)
    {
        $qb = $this->em->getRepository('SymbbCoreForumBundle:Post')->createQueryBuilder('p');
        $qb->select("p");
        $qb->where("p.topic = :topic ");
        $qb->orderBy("p.created", "ASC");
        $query = $qb->getQuery();
        $query->setParameter('topic', $topic->getId());
        $paginator = $this->paginator;
        $pagination = $paginator->paginate(
            $query, $pageNumber/* page number */, $limit/* limit per page */
        );
        return $pagination;
    }

    public function getBreadcrumbData(Post $object, TopicManager $topicManager, ForumManager $forumManager)
    {
        $breadcrumb = $topicManager->getBreadcrumbData($object->getTopic(), $forumManager);
        return $breadcrumb;
    }

    public function search($page = 1, $limit = 0){

        if($limit === null || $limit === 0){
            $limit = $this->configManager->get('newpost.max', "forum");
        }

        $configUsermanager  = $this->configManager->getSymbbConfig('usermanager');
        $configGroupManager = $this->configManager->getSymbbConfig('groupmanager');

        $userlcass = $configUsermanager['user_class'];
        $groupclass = $configGroupManager['group_class'];

        $sql = "SELECT
                    p
                FROM
                    SymbbCoreForumBundle:Post p
                INNER JOIN
                    SymbbCoreForumBundle:Topic t WITH
                    t.id = p.topic
                LEFT JOIN
                    SymbbCoreSystemBundle:Flag f WITH
                        f.objectClass = 'Symbb\Core\ForumBundle\Entity\Post' AND
                        f.objectId = p.id AND
                        f.user = :user AND
                        f.flag = 'new'
                WHERE
                    p.author != :user AND
                    (
                        ( SELECT COUNT(a.id) FROM SymbbCoreSystemBundle:Access a WHERE
                            a.objectId = t.forum AND
                            a.object = 'Symbb\Core\ForumBundle\Entity\Forum' AND
                            a.identity = :userclass AND
                            a.identityId = :user AND
                            a.access = 'VIEW'
                        ) > 0 OR
                        ( SELECT COUNT(a2.id) FROM SymbbCoreSystemBundle:Access a2 WHERE
                            a2.objectId = t.forum AND
                            a2.object = 'Symbb\Core\ForumBundle\Entity\Forum' AND
                            a2.identity = :groupclass AND
                            a2.identityId IN (:groups) AND
                            a2.access = 'VIEW'
                        ) > 0
                    )
                GROUP BY
                    p.id
                ORDER BY
                    f.id DESC,
                    p.created DESC ";

        $groupIds = array();
        foreach($this->getUser()->getGroups() as $group){
            $groupIds[]  = $group->getId();
        }

        //// count
        $query = $this->em->createQuery($sql);
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('count', 'count');
        $queryCount = $query->getSQL();
        $queryCount = "SELECT COUNT(*) count FROM (".$queryCount.") as temp";
        $queryCount = $this->em->createNativeQuery($queryCount, $rsm);
        $queryCount->setParameter(0, $this->getUser()->getId());
        $queryCount->setParameter(1, $this->getUser()->getId());
        $queryCount->setParameter(2, $userlcass);
        $queryCount->setParameter(3, $this->getUser()->getId());
        $queryCount->setParameter(4, $groupclass);
        $queryCount->setParameter(5, $groupIds);
        $count = $queryCount->getSingleScalarResult();
        ////

        if(!$count){
            $count = 0;
        }

        $query->setParameter('user', $this->getUser()->getId());
        $query->setParameter('userclass', $userlcass);
        $query->setParameter('groupclass', $groupclass);
        $query->setParameter('groups', $groupIds);

        $query->setHint('knp_paginator.count', $count);

        $pagination = $this->paginator->paginate(
            $query, $page, $limit, array('distinct' => false)
        );

        return $pagination;
    }

    /**
     * @param Post $post
     * @param $flag
     * @return bool
     */
    public function hasFlag(Post $post, $flag){
        return $this->postFlagHandler->checkFlag($post, $flag);
    }

    /**
     * @param Post $post
     * @return \Symbb\Core\SystemBundle\Entity\Flag[]
     */
    public function getFlags(Post $post){
        return $this->postFlagHandler->findAll($post);
    }



    /**
     * @param Post $post
     * @return bool
     */
    public function save(Post $post){
        $this->em->persist($post);
        $this->em->flush();
        return true;
    }
}
