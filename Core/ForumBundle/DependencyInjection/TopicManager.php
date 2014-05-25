<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\DependencyInjection;

use \Symfony\Component\Security\Core\SecurityContextInterface;
use SymBB\Core\ForumBundle\DependencyInjection\TopicFlagHandler;
use \SymBB\Core\SystemBundle\DependencyInjection\ConfigManager;

class TopicManager extends \SymBB\Core\SystemBundle\DependencyInjection\AbstractManager
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
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    public function __construct(
    TopicFlagHandler $topicFlagHandler, ConfigManager $configManager, $dispatcher
    )
    {
        $this->topicFlagHandler = $topicFlagHandler;
        $this->configManager = $configManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param int $topicId
     * @return \SymBB\Core\ForumBundle\Entity\Topic
     */
    public function find($topicId)
    {
        $post = $this->em->getRepository('SymBBCoreForumBundle:Topic')->find($topicId);
        return $post;
    }

    /**
     * 
     * @param int $topicId
     * @return array(<\SymBB\Core\ForumBundle\Entity\Topic>)
     */
    public function findPosts(\SymBB\Core\ForumBundle\Entity\Topic $topic, $page = 1, $limit = null, $orderDir = 'desc')
    {
        if ($limit === null) {
            $limit = $topic->getForum()->getEntriesPerPage();
        }

        $qbPage = $this->em->createQueryBuilder();
        $qbPage->add('select', 'count(p)')
            ->add('from', 'SymBBCoreForumBundle:Post p')
            ->add('where', 'p.topic = ?1')
            ->add('orderBy', 'p.created ' . strtoupper($orderDir))
            ->setParameter(1, $topic);
        $queryPage = $qbPage->getQuery();
        $count = $queryPage->getSingleScalarResult();

        $qb = $this->em->createQueryBuilder();
        $qb->add('select', 'p')
            ->add('from', 'SymBBCoreForumBundle:Post p')
            ->add('where', 'p.topic = ?1')
            ->add('orderBy', 'p.created ' . strtoupper($orderDir))
            ->setParameter(1, $topic);

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
     * @return \SymBB\Core\ForumBundle\DependencyInjection\TopicFlagHandler
     */
    public function getFlagHandler()
    {
        return $this->topicFlagHandler;
    }

    public function getBreadcrumbData(\SymBB\Core\ForumBundle\Entity\Topic $object, ForumManager $forumManager)
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
}
