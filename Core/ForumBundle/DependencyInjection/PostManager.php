<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\DependencyInjection;

use SymBB\Core\ForumBundle\Entity\Post;
use SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent;
use SymBB\Core\SystemBundle\DependencyInjection\AbstractManager;
use \SymBB\Core\SystemBundle\DependencyInjection\ConfigManager;
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

    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    public function __construct(
        PostFlagHandler $postFlagHandler, ConfigManager $configManager, $dispatcher
    )
    {
        $this->postFlagHandler = $postFlagHandler;
        $this->configManager = $configManager;
        $this->dispatcher = $dispatcher;
    }

    public function parseText(Post $post)
    {
        $text = $post->getText();
        $event = new PostManagerParseTextEvent($post, (string)$text);
        $this->dispatcher->dispatch('symbb.post.manager.parse.text', $event);
        $text = $event->getText();

        return $text;
    }

    public function cleanText(Post $post)
    {
        $text = $post->getText();
        $event = new PostManagerParseTextEvent($post, $text);
        $this->dispatcher->dispatch('symbb.post.manager.clean.text', $event);
        $text = $event->getText();
        return $text;
    }

    /**
     *
     * @param int $postId
     * @return \SymBB\Core\ForumBundle\Entity\Post
     */
    public function find($postId)
    {
        $post = $this->em->getRepository('SymBBCoreForumBundle:Post')->find($postId);
        return $post;
    }

    /**
     *
     * @param int $topicId
     * @return array(<\SymBB\Core\ForumBundle\Entity\Post>)
     */
    public function findByTopic(Topic $topic, $limit = null, $pageNumber = 1)
    {
        $qb = $this->em->getRepository('SymBBCoreForumBundle:Post')->createQueryBuilder('p');
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

    public function search($request){

        $page = $request->get('page');

        $limit = $this->configManager->get('newpost.max', "forum");

        $configUsermanager  = $this->configManager->getSymbbConfig('usermanager');
        $configGroupManager = $this->configManager->getSymbbConfig('groupmanager');

        $userlcass = $configUsermanager['user_class'];
        $groupclass = $configGroupManager['group_class'];

        $sql = "SELECT
                    p
                FROM
                    SymBBCoreForumBundle:Post p
                INNER JOIN
                    SymBBCoreForumBundle:Topic t WITH
                    t.id = p.topic
                LEFT JOIN
                    SymBBCoreSystemBundle:Flag f WITH
                        f.objectClass = 'SymBB\Core\ForumBundle\Entity\Post' AND
                        f.objectId = p.id AND
                        f.user = :user AND
                        f.flag = 'new'
                WHERE
                    p.author != :user AND
                    (
                        ( SELECT COUNT(a.id) FROM SymBBCoreSystemBundle:Access a WHERE
                            a.objectId = t.forum AND
                            a.object = 'SymBB\Core\ForumBundle\Entity\Forum' AND
                            a.identity = :userclass AND
                            a.identityId = :user AND
                            a.access = 'VIEW'
                        ) > 0 OR
                        ( SELECT COUNT(a2.id) FROM SymBBCoreSystemBundle:Access a2 WHERE
                            a2.objectId = t.forum AND
                            a2.object = 'SymBB\Core\ForumBundle\Entity\Forum' AND
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


}
