<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class FrontendApiController extends \SymBB\Core\SystemBundle\Controller\AbstractController
{

    /**
     * @Route("/api/forum/list/{parent}", name="symbb_api_forum_list", defaults={"parent" = 0})
     * @Method({"GET"})
     */
    public function forumListAction($parent){
        
        if($parent === 0){
            $parent = null;
        }
        
        $forumEnityList = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->findBy(array('parent' => $parent, 'type' => 'forum'), array('position' => 'asc', 'id' => 'asc'));
        
        $forumList = array();
        
        $forumListCheck = false;
        foreach ($forumEnityList as $key => $forum) {
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
            $access = $this->get('symbb.core.access.manager')->hasAccess();
            if ($access) {
                $forumList[] = $this->getForumAsArray($forum);
                $forumListCheck = true;
            };
        }
        
        $categoryEnityList = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->findBy(array('parent' => $parent, 'type' => 'category'), array('position' => 'asc', 'id' => 'asc'));

        $categoryList = array();
        
        foreach ($categoryEnityList as $key => $forum) {
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
            $access = $this->get('symbb.core.access.manager')->hasAccess();
            if ($access) {
                $categoryList[] = $this->getForumAsArray($forum);
            };
        }
        
        $params = array('categoryList' => $categoryList, 'forumList' => $forumList, 'forum' => null, 'forumListCheck' => $forumListCheck);
        $response = new Response(json_encode($params));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Forum $forum
     * @return type
     */
    protected function getForumAsArray($forum){
        $array = array();
        $array['id'] = $forum->getId();
        $array['name'] = $forum->getName();
        $array['description'] = $forum->getDescription();
        $array['topicCount'] = $forum->getTopicCount();
        $array['postCount'] = $forum->getPostCount();
        $array['backgroundImage'] = "";
        $array['flags'] = array();
        foreach($forum->getFlags() as $flag){
            $array['flags'][$flag->getFlag()] = $this->getFlagAsArray($flag);
        }
        $array['children'] = array();
        foreach($forum->getChildren() as $child){
            $array['children'][] = $this->getForumAsArray($child);
        }
        $array['lastPosts'] = array();
        $lastPosts = $this->get('symbb.core.forum.manager')->findPosts($forum, 10);
        foreach($lastPosts as $post){
            $array['lastPosts'][] = $this->getPostAsArray($post);
        }
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        if($forum->getImageName()){
            $array['backgroundImage'] = $helper->asset($forum, 'image');
        }
        return $array;
    }
    
    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Forum\Flag $flag
     * @return array
     */
    protected function getFlagAsArray($flag){
        $array = array();
        $flagName = $flag->getFlag();
        if($flagName == 'new'){
            $array['type'] = 'success';
        } else {
            $array['type'] = $flagName;
        }
        $array['title'] = $this->get('translator')->trans($flagName, array(), 'symbb_frontend');
        return $array;
    }
    
    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Post $post
     * @return array
     */
    protected function getPostAsArray($post){
        
        $array = array();
        $array['id'] = $post->getId();
        $array['name'] = $post->getName();
        $array['changed'] = $this->getCorrectTimestamp($post->getChanged());
        $array['author'] = $this->getAuthorAsArray($post->getAuthor());
        return $array;
    }
    
    /**
     * 
     * @param \SymBB\Core\UserBundle\Entity\UserInterface $author
     * @return array
     */
    protected function getAuthorAsArray($author){
        $array = array();
        $array['id'] = $author->getId();
        $array['username'] = $author->getUsername();
        $array['avatar'] = $this->get('symbb.core.user.manager')->getAbsoluteAvatarUrl($author);
        return $array;
    }
    
    protected function getCorrectTimestamp(\DateTime $datetime){
        $datetime->setTimezone($this->get('symbb.core.user.manager')->getTimezone());
        return $datetime->format(\DateTime::ISO8601);
    }
}