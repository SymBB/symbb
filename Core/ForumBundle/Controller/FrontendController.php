<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Controller;


class FrontendController extends \SymBB\Core\SystemBundle\Controller\AbstractController 
{
    
    public function indexAction(){
        return $this->portalAction();
    }
    
    public function portalAction(){
        $forumList = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->findBy(array('parent' => null));
        
        foreach($forumList as $key => $forum){
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
            $access = $this->get('symbb.core.access.manager')->hasAccess();
            if(!$access){
                unset($forumList[$key]);
            }
        }
        
        $params = array('forumList' => $forumList);
        return $this->render($this->getTemplateBundleName('portal').':Portal:index.html.twig', $params);
    }
    
    public function forumAction(){
        
        $forumList = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->findBy(array('parent' => null), array('position' => 'asc'));
        
        foreach($forumList as $key => $forum){
            $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
            $access = $this->get('symbb.core.access.manager')->hasAccess();
            if(!$access){
                unset($forumList[$key]);
            }
        }
        
        $params = array('forumList' => $forumList, 'forum' => null);
        return $this->render($this->getTemplateBundleName('forum').':Forum:show.html.twig', $params);
    }
    
    public function forumShowAction($name, $id){
        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($id);
        
        $this->get('symbb.core.access.manager')->addAccessCheck('SYMBB_FORUM#VIEW', $forum, $this->getUser());
        $this->get('symbb.core.access.manager')->checkAccess();
        
        $params = array('forum' => $forum, 'forumList' => $forum->getChildren());
        return $this->render($this->getTemplateBundleName('forum').':Forum:show.html.twig', $params);
    }
    
    public function newestAction(){
        
        $posts = $this->get('symbb.core.forum.manager')->findNewestPosts(null, 50);
        
        return $this->render($this->getTemplateBundleName('forum').':Forum:newest.html.twig', array('posts' => $posts));
    }
    
    public function ignoreAction($forum){
        
        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($forum);
        
        if(is_object($forum)){
            $this->ignoreForum($forum);
        }
        
        return $this->forward('SymBBCoreForumBundle:Frontend:forumShow', array(
                'name' => $forum->getSeoName(),
                'id'  => $forum->getId()
            ));
    }


    public function watchAction($forum){
        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($forum);
        if(is_object($forum)){
            $this->watchForum($forum);
        }
        return $this->forward('SymBBCoreForumBundle:Frontend:forumShow', array(
                'name' => $forum->getSeoName(),
                'id'  => $forum->getId()
            ));
    }


    public function readAction($forum){
        $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
            ->find($forum);
        if(is_object($forum)){
            $this->readForum($forum);
        }
        return $this->forward('SymBBCoreForumBundle:Frontend:forumShow', array(
                'name' => $forum->getSeoName(),
                'id'  => $forum->getId()
            ));
    }
    
    protected function ignoreForum(\SymBB\Core\ForumBundle\Entity\Forum $forum){
        $flagHandler = $this->get('symbb.core.forum.flag');
        $flagHandler->insertFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach($subForms as $subForm){
            $this->ignoreForum($subForm);
        }
    }
    
    protected function watchForum(\SymBB\Core\ForumBundle\Entity\Forum $forum){
        $flagHandler = $this->get('symbb.core.forum.flag');
        $flagHandler->removeFlag($forum, 'ignore');
        $subForms = $forum->getChildren();
        foreach($subForms as $subForm){
            $this->watchForum($subForm);
        }
    }
    
    protected function readForum(\SymBB\Core\ForumBundle\Entity\Forum $forum){
        
        $topics = $forum->getTopics();
        foreach($topics as $topic){
            $flagHandler = $this->get('symbb.core.topic.flag');
            $flagHandler->removeFlag($topic, 'new');
        }
        
        $subForms = $forum->getChildren();
        foreach($subForms as $subForm){
            $this->readAction($subForm);
        }
        
    }
    
}