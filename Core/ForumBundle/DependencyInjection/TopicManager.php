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
    SecurityContextInterface $securityContext, TopicFlagHandler $topicFlagHandler, ConfigManager $configManager, $em, $dispatcher
    )
    {
        $this->securityContext = $securityContext;
        $this->topicFlagHandler = $topicFlagHandler;
        $this->configManager = $configManager;
        $this->em = $em;
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
}
