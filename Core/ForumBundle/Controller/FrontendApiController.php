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
     * @Route("/api/forum/{forum}/ignore", name="symbb_api_forum_ignore")
     * @Method({"GET"})
     */
    public function forumIgnore($forum){
        
    }
    
    /**
     * @Route("/api/forum/{forum}/unignore", name="symbb_api_forum_unignore")
     * @Method({"GET"})
     */
    public function forumUnignore($forum){  
        
    }
    
    /**
     * @Route("/api/forum/{forum}/markAsRead", name="symbb_api_forum_mark_as_read")
     * @Method({"GET"})
     */
    public function forumMarkAsRead($forum){  
        
    }
    
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
        
        $hasForumList = false;
        foreach ($forumEnityList as $key => $forum) {
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
            $access = $this->get('symbb.core.access.manager')->hasAccess();
            if ($access) {
                $forumList[] = $this->getForumAsArray($forum);
                $hasForumList = true;
            };
        }
        
        $categoryEnityList = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->findBy(array('parent' => $parent, 'type' => 'category'), array('position' => 'asc', 'id' => 'asc'));

        $categoryList = array();
        $hasCategoryList = false;
        foreach ($categoryEnityList as $key => $forum) {
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
            $access = $this->get('symbb.core.access.manager')->hasAccess();
            if ($access) {
                $categoryList[] = $this->getForumAsArray($forum);
                $hasCategoryList = true;
            };
        }
        
        
        $topicList = array();
        $hasTopicList = false;
        $writeAccess = false;
        if($parent > 0){
            $parent = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->find($parent);
            $topics = $this->get('symbb.core.forum.manager')->findTopics($parent);
            foreach($topics as $topic){
                $topicList[] = $this->getTopicAsArray($topic);
                $hasTopicList = true;
            }
        
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#CREATE_TOPIC', $parent, $this->getUser());
            $writeAccess = $this->get('symbb.core.access.manager')->hasAccess();
        }
        
        
        $breadcrumbItems = $this->get('symbb.core.forum.manager')->getBreadcrumbData($parent);
        
        $params = array(
            'forum' => $this->getForumAsArray($parent),
            'categoryList' => $categoryList, 
            'forumList' => $forumList, 
            'topicList' => $topicList,
            'hasForumList' => $hasForumList, 
            'hasCategoryList' => $hasCategoryList, 
            'hasTopicList' => $hasTopicList, 
            'breadcrumbItems' => $breadcrumbItems,
            'access' => array(
                'write' => $writeAccess
            )
         );
        
        $response = new Response(json_encode($params));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Topic $topic
     * @return array
     */
    protected function getTopicAsArray(\SymBB\Core\ForumBundle\Entity\Topic $topic = null){
        $array = array();
        if(is_object($topic)){
            $array['id'] = $topic->getId();
            $array['name'] = $topic->getName();
            $array['postCount'] = $topic->getPostCount();
            $array['backgroundImage'] = $this->get('symbb.core.user.manager')->getAvatar($topic->getAuthor());
            $array['flags'] = array();
            foreach($topic->getFlags() as $flag){
                $array['flags'][$flag->getFlag()] = $this->getFlagAsArray($flag);
            }
            $array['posts'] = array();
            $lastPosts = $this->get('symbb.core.topic.manager')->findPosts($topic);
            foreach($lastPosts as $post){
                $array['posts'][] = $this->getPostAsArray($post);
            }
            $array['seo']['name'] = $topic->getSeoName();
        }
        return $array;
    }
    
    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Forum $forum
     * @return type
     */
    protected function getForumAsArray(\SymBB\Core\ForumBundle\Entity\Forum $forum = null){
        $array = array();
        if(is_object($forum)){
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
            $array['seo']['name'] = $forum->getSeoName();
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
        } else if($flagName == 'answered'){
            $array['type'] = 'warning';
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
    protected function getPostAsArray(\SymBB\Core\ForumBundle\Entity\Post $post = null){
        
        $array = array();
        $array['id'] = $post->getId();
        $array['name'] = $post->getName();
        $array['changed'] = $this->getCorrectTimestamp($post->getChanged());
        $array['author'] = $this->getAuthorAsArray($post->getAuthor());
        $array['seo']['name'] = $post->getSeoName();
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