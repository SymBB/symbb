<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Controller;

class FrontendPostController extends \SymBB\Core\SystemBundle\Controller\AbstractController 
{
    
    protected $topic = null;
    protected $post = null;
    protected $forum = null;
    
    /**
     * edit a post
     * @param type $name
     * @param type $topic
     * @param type $post
     * @return type
     */
    public function editAction($post){

        $post       = $this->getPostById($post);
        $topic      = $post->getTopic();
                
        return $this->doEdit($topic, $post);
    }
    
    /**
     * @param type $name
     * @param type $topic
     * @return type
     */
    public function newAction($topic){       
        $topic = $this->getTopicById($topic);
        return $this->doEdit($topic);
    }
    
    public function doEdit(\SymBB\Core\ForumBundle\Entity\Topic $topic, $post = null){
        
                
        if($post && $topic->getMainPost()->getId() == $post->getId()){
            return $this->forward('SymBBCoreForumBundle:FrontendTopic:edit', array(
                'topic'  => $topic->getId()
            ));
        }
        
        // check only for "edit" case
        if(is_object($post) && $post->getId() > 0){
            $accessService  = $this->get('symbb.core.access.manager');
            // check if you have access to edit the post
            $accessService->addAccessCheck('SYMBB_POST#EDIT', $post);
            $accessService->checkAccess();
        } else {
            $accessService  = $this->get('symbb.core.access.manager');
            $accessService->addAccessCheck('SYMBB_FORUM#CREATE_POST', $topic->getForum());
            $accessService->checkAccess();
        }
        
        $form       = null;
        $saved      = $this->handlePostRequest($form, $topic, $post);
        
        $params = array('topic' => $topic);
        $params['form']     = $form->createView();
        $params['bbcodes']  = $this->getBBCodes();
        $params['saved']    = $saved;
        $params['post']     = $post;
        
        if($saved){
            return $this->render($this->getTemplateBundleName('forum').':Post:saved.html.twig', $params);
        }
        
        return $this->render($this->getTemplateBundleName('forum').':Post:edit.html.twig', $params);
    }
    
    /**
     * 
     * @param \SymBB\Core\ForumBundle\Entity\Post $post
     * @return type
     */
    public function deleteAction(\SymBB\Core\ForumBundle\Entity\Post $post){
        
        $accessService  = $this->get('symbb.core.access.manager');
        $accessService->addAccessCheck('SYMBB_POST#DELETE', $post);
        $accessService->checkAccess();
        
        $em         = $this->getDoctrine()->getManager('symbb');
        $topic      = $post->getTopic();
        $firstPost  = $topic->getPosts()->first();
        if($firstPost->getId() == $post->getId()){
            return $this->forward('SymBBCoreForumBundle:FrontendTopic:delete', array(
                'topic'  => $topic->getId()
            ));
        }
        $em->remove($post);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('Your post has been deleted successfully. You will now be redirected ...', array(), 'symbb_frontend')
        );
        
        return $this->render($this->getTemplateBundleName('forum').':Post:delete.html.twig', array('topic' => $topic));
    }
    
    /**
     * 
     * @param type $topic
     * @return \SymBB\Core\ForumBundle\Entity\Forum
     */
    protected function getForumById($forum){
        if($this->forum === null){
            $this->forum      = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')->find($forum);
        }
        return $this->forum;
    }
    
    /**
     * 
     * @param type $topic
     * @return \SymBB\Core\ForumBundle\Entity\Topic
     */
    protected function getTopicById($topic){
        if($this->topic === null){
            $this->topic      = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Topic', 'symbb')->find($topic);
        }
        return $this->topic;
    }
    
    /**
     * 
     * @param type $post
     * @return \SymBB\Core\ForumBundle\Entity\Post
     */
    protected function getPostById($post){
        if($this->post === null){
            $this->post      = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Post', 'symbb')->find($post);
        }
        return $this->post;
    }

    
    /**
     * handle the Post Request and save it
     * @param type $form
     * @param \SymBB\Core\ForumBundle\Entity\Topic $topic
     * @param type $post
     * @return type
     */
    protected function handlePostRequest(&$form, \SymBB\Core\ForumBundle\Entity\Topic &$topic, &$post){
        
        
        $form   = $this->getPostForm($topic, $post);
        $oldId  = $post->getId();

        $form->handleRequest($this->get('request'));
        $data = $this->get('request')->get('post');
        
        $event      = new \SymBB\Core\EventBundle\Event\EditPostEvent($post, $form);
        $this->get('event_dispatcher')->dispatch('symbb.post.controller.handle.request', $event);
        
        if ($form->isValid()) {
            
            $topic->setChangedValue(new \DateTime());
            
            $em = $this->getDoctrine()->getManager('symbb');
            
            $event      = new \SymBB\Core\EventBundle\Event\EditPostEvent($post, $form);
            $this->get('event_dispatcher')->dispatch('symbb.post.controller.save', $event);
            
            
            $em->persist($post);
            $em->persist($topic);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('Your post has been saved successfully You will now be redirected ...', array(), 'symbb_frontend')
            );
            
            //only if the id was "0" ( only by "new" )
            if($oldId === null){
                // set access to owner
                $accessService  = $this->get('symbb.core.access.manager');
                $accessService->grantAccess('SYMBB_POST#OWNER', $post);
                
                // adding new flags to all user and a answered flag for the current user
                $this->get('symbb.core.post.flag')->insertFlags($post, 'new');
                $this->get('symbb.core.topic.flag')->insertFlags($topic, 'new');
                $this->get('symbb.core.forum.flag')->insertFlags($topic->getForum(), 'new');
                // answered
                $this->get('symbb.core.topic.flag')->insertFlag($topic, 'answered');
            }
            
            if(isset($data['notifyMe']) && $data['notifyMe']){
                $this->get('symbb.core.topic.flag')->insertFlag($topic, 'notify');
            } else {
                $this->get('symbb.core.topic.flag')->removeFlag($topic, 'notify');
            }
            
            // check notify me
            $users  = $this->get('symbb.core.topic.flag')->getUsersForFlag('notify', $topic);
            foreach($users as $notifyUser => $timestamp){
                if($notifyUser != $this->getUser()->getId()){
                    $this->get('symbb.core.forum.notify')->sendTopicNotifications($topic, $notifyUser);
                }
            }
            
            return true;
        }
        return false;
    }

    
    /**
     * generate a new Form object
     * @param type $topic
     * @param type $post
     * @return type
     */
    protected function getPostForm($topic, &$post){
        
        // check if form was submited
        $postId = $this->get('request')->get('form_id');
        
        if($postId > 0){
            $post   = $this->getPostById($postId);
        }
   
        // if no post information than create an new empty one
        if($post === null){
            $post = \SymBB\Core\ForumBundle\Entity\Post::createNew($topic, $this->getUser());
        }
        
        if($post->getId() > 0){
            $url    = $this->generateUrl('symbb_edit_post', array('post' => $post->getId()));
        } else {
            $url    = $this->generateUrl('symbb_new_post', array('topic' => $topic->getId()));
        }
        
        $dispatcher = $this->get('event_dispatcher');
        $formType   = new \SymBB\Core\ForumBundle\Form\Type\PostType($url, $post, $dispatcher, $this->get('translator'));
        
        
        $form   = $this->createForm($formType, $post);
        
        if($this->get('symbb.core.topic.flag')->checkFlag($topic, 'notify')){
            $form->get('notifyMe')->setData(true);
        }
        
        
        return $form;
    }

    /**
     * get a list of grouped BBCodes
     * @return array
     */
    protected function getBBCodes(){
        $bbcodes     = array();
        $decoda    = $this->get('fm_bbcode.decoda_manager')->get('filters', 'default');
        $filters    = $decoda->getFilters();
        foreach($filters as $filterKey => $filter){
            $tags = $filter->getTags();
            $groupName = $this->get('translator')->trans($filterKey, array(), 'symbb_frontend');
            foreach($tags as $tag => $conf){
                if(
                    (
                        $filterKey == 'email' && $tag == 'mail'
                    ) ||
                    (
                        $filterKey == 'url' && $tag == 'link'
                    ) ||
                    (
                        $filterKey == 'image' && $tag == 'img'
                    )
                ){
                    // double bbcode...
                    continue;
                }
                $bbcodes[$groupName][] = array('tag' => $tag, 'name' => $tag);
            }
        }
        return $bbcodes;
    }
    
    
}