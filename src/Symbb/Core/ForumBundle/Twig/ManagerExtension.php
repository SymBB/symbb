<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Twig;

use \SymBB\Core\ForumBundle\DependencyInjection\PostManager;
use \SymBB\Core\ForumBundle\DependencyInjection\ForumManager;

class ManagerExtension extends \Twig_Extension
{

    /**
     *
     * @var \SymBB\Core\ForumBundle\DependencyInjection\PostManager 
     */
    protected $postManager;
    
    /**
     *
     * @var \SymBB\Core\ForumBundle\DependencyInjection\ForumManager 
     */
    protected $forumManager;

    public function __construct(PostManager $postManager, ForumManager $forumManager)
    {
        $this->postManager = $postManager;
        $this->forumManager = $forumManager;

    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbPostManager', array($this, 'getPostManager')),
            new \Twig_SimpleFunction('getSymbbForumManager', array($this, 'getForumManager'))
        );

    }

    /**
     * 
     * @return \SymBB\Core\ForumBundle\DependencyInjection\PostManager
     */
    public function getPostManager()
    {
        return $this->postManager;

    }

    /**
     * 
     * @return \SymBB\Core\ForumBundle\DependencyInjection\ForumManager
     */
    public function getForumManager()
    {
        return $this->forumManager;

    }

    public function getName()
    {
        return 'symbb_forum_managers';

    }
}