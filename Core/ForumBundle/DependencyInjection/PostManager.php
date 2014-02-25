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
use SymBB\Core\ForumBundle\DependencyInjection\PostFlagHandler;
use \SymBB\Core\SystemBundle\DependencyInjection\ConfigManager;

class PostManager extends \SymBB\Core\SystemBundle\DependencyInjection\AbstractManager
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
    SecurityContextInterface $securityContext, PostFlagHandler $postFlagHandler, ConfigManager $configManager, $em, $dispatcher
    )
    {
        $this->securityContext = $securityContext;
        $this->postFlagHandler = $postFlagHandler;
        $this->configManager = $configManager;
        $this->em = $em;
        $this->dispatcher = $dispatcher;

    }

    public function parseText(\SymBB\Core\ForumBundle\Entity\Post $post)
    {
        $text = $post->getText();
        $event = new \SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent($post, $text);
        $this->dispatcher->dispatch('symbb.post.manager.parse.text', $event);
        $text = $event->getText();
        
        return $text;

    }

    public function cleanText(\SymBB\Core\ForumBundle\Entity\Post $post)
    {
        $text = $post->getText();
        $event = new \SymBB\Core\ForumBundle\Event\PostManagerParseTextEvent($post, $text);
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
}
