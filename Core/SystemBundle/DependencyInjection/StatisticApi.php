<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\DependencyInjection;

use SymBB\Core\ForumBundle\DependencyInjection\ForumManager;
use SymBB\Core\ForumBundle\DependencyInjection\PostManager;
use SymBB\Core\ForumBundle\DependencyInjection\TopicManager;

class StatisticApi extends AbstractApi
{

    /**
     * @return int
     */
    public function getPostCount(){
        $entries = $this->em->getRepository('SymBBCoreForumBundle:Post')->findAll();
        return count($entries);
    }


    /**
     * @return int
     */
    public function getTopicCount(){
        $entries = $this->em->getRepository('SymBBCoreForumBundle:Topic')->findAll();
        return count($entries);
    }


    /**
     * @return int
     */
    public function getActiveUserCount(){
        $users = $this->userManager->findUsers();
        return count($users);
    }


    /**
     * @return int
     */
    public function getInactiveUserCount(){
        return 0;
    }

}