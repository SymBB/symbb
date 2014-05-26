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
        if ($object->getId() > 0) {
            $breadcrumb[] = array(
                'type' => 'topic',
                'name' => $object->getTopic()->getName(),
                'seoName' => $object->getTopic()->getSeoName(),
                'id' => $object->getTopic()->getid()
            );
        }
        return $breadcrumb;
    }
}
