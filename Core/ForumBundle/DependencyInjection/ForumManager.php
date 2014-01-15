<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\DependencyInjection;

use \SymBB\Core\ForumBundle\Entity\Forum;
use \Symfony\Component\Security\Core\SecurityContextInterface;
use SymBB\Core\ForumBundle\DependencyInjection\TopicFlagHandler;
use SymBB\Core\ForumBundle\DependencyInjection\PostFlagHandler;

class ForumManager
{
    
    /**
     *
     * @var SecurityContextInterface 
     */
    protected $securityContext;
    /**
     *
     * @var TopicFlagHandler
     */
    protected $topicFlagHandler;
    /**
     *
     * @var PostFlagHandler
     */
    protected $postFlagHandler;
    
    public function __construct(SecurityContextInterface $securityContext, TopicFlagHandler $topicFlagHandler, PostFlagHandler $postFlagHandler, $em)
    {
        $this->securityContext  = $securityContext;
        $this->topicFlagHandler = $topicFlagHandler;
        $this->postFlagHandler  = $postFlagHandler;
        $this->em               = $em;
    }
    
    public function getUser(){
        if(!is_object($this->user)){
            $this->user = $this->securityContext->getToken()->getUser();
        }
        return $this->user;
    }
    
    public function findNewestTopics(Forum $parent = null)
    {
        $topics = $this->topicFlagHandler->findTopicsByFlag('new', $parent);
        return $topics;
    }
    
    public function findNewestPosts(Forum $parent = null, $limit = null)
    {
        $posts = $this->postFlagHandler->findPostsByFlag('new', $parent, null, true, $limit);
        return $posts;
    }
    
    
    
    public function getSelectList($types = array())
    {
        $repo   = $this->em->getRepository('SymBBCoreForumBundle:Forum');
        $list   = array();
        $by     = array('parent' => null);
        if(!empty($types)){
            $by['type'] = $types;
        }
        $entries = $repo->findBy($by, array('position' => 'ASC', 'name' => 'ASC'));
        foreach ($entries as $entity) {
            $list[$entity->getId()] = $entity;
            $this->addChildsToArray($entity, $list);
        }

        $listFinal = array();
        
        foreach($list as $forum){
            $name = ' '.$forum->getName();
            $name = $this->addSpaceForParents($forum, $name);
            $listFinal[$forum->getId()] = $name;
        }
        
        return $listFinal;

    }
    
    private function addSpaceForParents($forum, $name){
        $parent = $forum->getParent();
        if(is_object($parent)){
            $name = '─'.$name;
            $name = $this->addSpaceForParents($parent, $name);
        }
        return $name;
    }

    private function addChildsToArray($entity, &$array)
    {
        $childs = $entity->getChildren();
        if (!empty($childs) && count($childs) > 0) {
            foreach ($childs as $child) {
                $array[$child->getId()] = $child;
                $this->addChildsToArray($child, $array);
            }
        }

    }
    
}
