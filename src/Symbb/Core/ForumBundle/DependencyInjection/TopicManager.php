<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\DependencyInjection;

use Symbb\Core\ForumBundle\Entity\Topic;
use \Symbb\Core\SystemBundle\Manager\ConfigManager;

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

    public function __construct(
    TopicFlagHandler $topicFlagHandler, ConfigManager $configManager
    )
    {
        $this->topicFlagHandler = $topicFlagHandler;
        $this->configManager = $configManager;
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
     * @return array(<\Symbb\Core\ForumBundle\Entity\Topic>)
     */
    public function findPosts(\Symbb\Core\ForumBundle\Entity\Topic $topic, $page = 1, $limit = null, $orderDir = 'desc')
    {
        if ($limit === null) {
            $limit = $topic->getForum()->getEntriesPerPage();
        }

        $qbPage = $this->em->createQueryBuilder();
        $qbPage->add('select', 'count(p)')
            ->add('from', 'SymbbCoreForumBundle:Post p')
            ->add('where', 'p.topic = ?1')
            ->add('orderBy', 'p.created ' . strtoupper($orderDir))
            ->setParameter(1, $topic->getId());
        $queryPage = $qbPage->getQuery();
        $count = $queryPage->getSingleScalarResult();

        $qb = $this->em->createQueryBuilder();
        $qb->add('select', 'p')
            ->add('from', 'SymbbCoreForumBundle:Post p')
            ->add('where', 'p.topic = ?1')
            ->add('orderBy', 'p.created ' . strtoupper($orderDir))
            ->setParameter(1, $topic->getId());

        $query = $qb->getQuery();
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
    public function markAsRead(Topic $topic){
        $this->topicFlagHandler->removeFlag($topic, 'new');
        return true;
    }

    /**
     * @param Topic $topic
     * @param string $flag
     * @return bool
     */
    public function checkFlag(Topic $topic, $flag = 'new'){
        foreach ($this->topicFlagHandler->findAll($topic) as $flag) {
            if($flag->getFlag() == 'new'){
                return true;
            }
        }
    }

    /**
     * @param Topic $topic
     * @return bool
     */
    public function save(Topic $topic){
        $this->em->persist($topic);
        $this->em->persist($topic->getMainPost());
        $this->em->flush();
        return true;
    }


    public function getParticipatedTopics($page = 1, $limit = 20)
    {

        $sql = "SELECT
                    t
                FROM
                    SymbbCoreForumBundle:Topic t
                INNER JOIN
                    SymbbCoreForumBundle:Post p WITH
                    p.topic = t.id
                WHERE
                    p.author = :user
                GROUP BY
                    t.id
                ORDER BY
                    p.created DESC ";

        //// count
        $query = $this->em->createQuery($sql);
        $query->setParameter('user', $this->getUser()->getId());

        $pagination = $this->createPagination($query, $page, $limit);

        return $pagination;
    }
}
