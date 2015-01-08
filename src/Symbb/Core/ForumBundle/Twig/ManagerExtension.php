<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Twig;

use \Symbb\Core\ForumBundle\DependencyInjection\PostManager;
use \Symbb\Core\ForumBundle\DependencyInjection\ForumManager;
use Symbb\Core\ForumBundle\DependencyInjection\TopicManager;

class ManagerExtension extends \Twig_Extension
{

    /**
     *
     * @var \Symbb\Core\ForumBundle\DependencyInjection\PostManager
     */
    protected $postManager;
    
    /**
     *
     * @var \Symbb\Core\ForumBundle\DependencyInjection\ForumManager
     */
    protected $forumManager;

    /**
     *
     * @var \Symbb\Core\ForumBundle\DependencyInjection\TopicManager
     */
    protected $topicManager;

    public function __construct(PostManager $postManager, ForumManager $forumManager, TopicManager $topicManager)
    {
        $this->postManager = $postManager;
        $this->forumManager = $forumManager;
        $this->topicManager = $topicManager;

    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbPostManager', array($this, 'getPostManager')),
            new \Twig_SimpleFunction('getSymbbForumManager', array($this, 'getForumManager')),
            new \Twig_SimpleFunction('getSymbbTopicManager', array($this, 'getTopicManager'))
        );

    }

    /**
     * 
     * @return \Symbb\Core\ForumBundle\DependencyInjection\PostManager
     */
    public function getPostManager()
    {
        return $this->postManager;

    }

    /**
     * 
     * @return \Symbb\Core\ForumBundle\DependencyInjection\ForumManager
     */
    public function getForumManager()
    {
        return $this->forumManager;

    }

    /**
     *
     * @return \Symbb\Core\ForumBundle\DependencyInjection\TopicManager
     */
    public function getTopicManager()
    {
        return $this->topicManager;

    }

    public function getName()
    {
        return 'symbb_forum_managers';

    }
}