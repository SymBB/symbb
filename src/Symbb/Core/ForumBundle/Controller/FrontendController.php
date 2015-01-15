<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Controller;


use Symbb\Core\ForumBundle\Entity\Forum;
use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\ForumBundle\Event\PostFormSaveEvent;
use Symbb\Core\ForumBundle\Event\TopicFormSaveEvent;
use Symbb\Core\ForumBundle\Form\TopicType;
use Symbb\Core\ForumBundle\Security\Authorization\ForumVoter;
use Symbb\Core\ForumBundle\Security\Authorization\PostVoter;
use Symbb\Core\ForumBundle\Security\Authorization\TopicVoter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FrontendController
 * @package Symbb\Core\ForumBundle\Controller
 */
class FrontendController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{

    /**
     * @return mixed
     */
    public function indexAction()
    {
        $forum = new Forum();
        $topics = null;
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array("forum" => $forum, "topics" => $topics));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function searchForumAction(Request $request){
        $page = $request->get("page", 1);
        $posts = $this->get('symbb.core.post.manager')->search($page);
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:search.html.twig', array("posts" => $posts));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function showForumAction(Request $request, $id)
    {
        $forum = $this->get("symbb.core.forum.manager")->find($id);
        $topics = null;
        if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::VIEW, $forum)) {
            throw $this->createAccessDeniedException();
        }
        if($forum->isForum()){
            $topics = $this->get("symbb.core.forum.manager")->findTopics($forum, $request->get("page"));
        }
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:index.html.twig', array("forum" => $forum, "topics" => $topics));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function markAsReadForumAction(Request $request, $id){
        $forum = $this->get("symbb.core.forum.manager")->find($id);
        if(is_object($forum) && $forum->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::VIEW, $forum)) {
                throw $this->createAccessDeniedException();
            }
            $success = $this->get("symbb.core.forum.manager")->markAsRead($forum);
            if($success){
                $this->addSuccess("The forum has been marked as read.", $request);
            } else {
                $this->addError("An error occurred while marking the forum as read.", $request);
            }
        } else {
            $this->addError("Forum not found.", $request);
        }
        return $this->returnToLastPage($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function ignoreForumAction(Request $request, $id){
        $forum = $this->get("symbb.core.forum.manager")->find($id);
        if(is_object($forum) && $forum->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::VIEW, $forum)) {
                throw $this->createAccessDeniedException();
            }
            $success = $this->get("symbb.core.forum.manager")->ignoreForum($forum);
            if($success){
                $this->addSuccess("Forum was ignored.", $request);
            } else {
                $this->addError("Error while ignoring the forum.", $request);
            }
        } else {
            $this->addError("Forum not found.", $request);
        }
        return $this->returnToLastPage($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function unignoreForumAction(Request $request, $id){
        $forum = $this->get("symbb.core.forum.manager")->find($id);
        if(is_object($forum) && $forum->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::VIEW, $forum)) {
                throw $this->createAccessDeniedException();
            }
            $success = $this->get("symbb.core.forum.manager")->unignoreForum($forum);
            if($success){
                $this->addSuccess("Forum was ignored.", $request);
            } else {
                $this->addError("Error while ignoring the forum.", $request);
            }
        } else {
            $this->addError("Forum not found.", $request);
        }
        return $this->returnToLastPage($request);
    }



    /**
     * @param Request $request
     * @return mixed
     */
    public function markAsReadTopicAction(Request $request, $id){
        $topic = $this->get("symbb.core.topic.manager")->find($id);
        if(is_object($topic) && $topic->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(TopicVoter::VIEW, $topic)) {
                throw $this->createAccessDeniedException();
            }
            $success = $this->get("symbb.core.topic.manager")->markAsRead($topic);
            if($success){
                $this->addSuccess("The topic has been marked as read.", $request);
            } else {
                $this->addError("An error occurred while marking the topic as read.", $request);
            }
        } else {
            $this->addError("Topic not found.", $request);
        }
        return $this->returnToLastPage($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function moveTopicAction(Request $request){

        $topicId = (int)$request->get("topic");
        $topic = $this->get("symbb.core.topic.manager")->find($topicId);
        if(is_object($topic) && $topic->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(TopicVoter::MOVE, $topic)) {
                throw $this->createAccessDeniedException();
            }
            $targetForumId = (int)$request->get("targetForum");
            $forum = $this->get("symbb.core.forum.manager")->find($targetForumId);

            if(is_object($forum) && $forum->getId() > 0){
                $success = $this->get("symbb.core.topic.manager")->move($topic, $forum);

                if($success){
                    $this->addSuccess("Topic was moved.", $request);
                } else {
                    $this->addError("Error while moving the topic.", $request);
                }
            } else {
                $this->addError("Error while moving the topic: Target forum not found.", $request);
            }
        } else {
            $this->addError("Topic not found.", $request);
        }
        return $this->returnToLastPage($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function deleteTopicAction(Request $request, $id){
        $topic = $this->get("symbb.core.topic.manager")->find($id);
        if(is_object($topic) && $topic->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(TopicVoter::DELETE, $topic)) {
                throw $this->createAccessDeniedException();
            }
            $success = $this->get("symbb.core.topic.manager")->delete($topic);
            if($success){
                $this->addSuccess("Topic was deleted.", $request);
            } else {
                $this->addError("Error while deleteing the topic.", $request);
            }
        } else {
            $this->addError("Topic not found.", $request);
        }
        return $this->returnToLastPage($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function closeTopicAction(Request $request, $id){
        $topic = $this->get("symbb.core.topic.manager")->find($id);
        if(is_object($topic) && $topic->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(TopicVoter::EDIT, $topic)) {
                throw $this->createAccessDeniedException();
            }
            $success = $this->get("symbb.core.topic.manager")->close($topic);
            if($success){
                $this->addSuccess("Topic was closed.", $request);
            } else {
                $this->addError("Error while closing the topic.", $request);
            }
        } else {
            $this->addError("Topic not found.", $request);
        }
        return $this->returnToLastPage($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function openTopicAction(Request $request, $id){
        $topic = $this->get("symbb.core.topic.manager")->find($id);
        if(is_object($topic) && $topic->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(TopicVoter::EDIT, $topic)) {
                throw $this->createAccessDeniedException();
            }
            $success = $this->get("symbb.core.topic.manager")->open($topic);
            if($success){
                $this->addSuccess("Topic was open opened.", $request);
            } else {
                $this->addError("Error while opening the topic.", $request);
            }
        } else {
            $this->addError("Topic not found.", $request);
        }
        return $this->returnToLastPage($request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function viewTopicAction(Request $request){
        $id = $request->get('id');
        $topic = $this->get("symbb.core.topic.manager")->find($id);
        if(is_object($topic) && $topic->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(TopicVoter::VIEW, $topic)) {
                throw $this->createAccessDeniedException();
            }
            $page = $request->get("page");
            $posts = $this->get("symbb.core.topic.manager")->findPosts($topic, $page, null, "asc");
            return $this->render(
                $this->getTemplateBundleName('forum') . ':Forum:topicView.html.twig',
                array("topic" => $topic, "posts" => $posts)
            );
        } else {
            $this->addError("Topic not found.", $request);
        }
        return $this->returnToLastPage($request);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createTopicAction(Request $request){
        $forumId = $request->get("forum");
        $forum = $this->get('symbb.core.forum.manager')->find($forumId);

        if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::CREATE_TOPIC, $forum) || $forum->getId() <= 0) {
            throw $this->createAccessDeniedException();
        }

        $topic = new Topic();
        $post = new Post();
        $post->setAuthor($this->getUser());
        $topic->setAuthor($this->getUser());
        $post->setTopic($topic);
        $topic->setForum($forum);
        $topic->setMainPost($post);
        return $this->handleTopic($request, $topic);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function editPostAction(Request $request){
        $postId = $request->get("id");
        $post = $this->get('symbb.core.post.manager')->find($postId);

        if (!$this->get('security.authorization_checker')->isGranted(PostVoter::EDIT, $post)) {
            throw $this->createAccessDeniedException();
        }

        if($post->getTopic()->getMainPost()->getId() == $post->getId()){
            return $this->handleTopic($request, $post->getTopic());
        } else {
            return $this->handlePost($request, $post);
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createPostAction(Request $request){
        $topicId = $request->get("topic");
        $topic = $this->get('symbb.core.topic.manager')->find($topicId);

        if(is_object($topic)){
            if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::CREATE_POST, $topic->getForum())) {
                throw $this->createAccessDeniedException();
            }

            $post = new Post();
            $post->setAuthor($this->getUser());
            $post->setTopic($topic);
            $post->setName($this->get("translator")->trans("Re:", array(), "symbb_frontend")." ".$topic->getName());

            return $this->handlePost($request, $post);
        }

        $this->addError("Topic not found!", $request);
        return $this->returnToLastPage($request);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function quotePostAction(Request $request){
        $topicId = $request->get("topic");
        $quoteId = $request->get("quoteId");
        $topic = $this->get('symbb.core.topic.manager')->find($topicId);

        if(is_object($topic)){

            $quotePost = $this->get('symbb.core.post.manager')->find($quoteId);
            if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::CREATE_POST, $topic->getForum())) {
                throw $this->createAccessDeniedException();
            }
            $post = new Post();
            $post->setAuthor($this->getUser());
            $post->setText("[quote=".$quotePost->getAuthor()->getUsername()."]".$quotePost->getText()."[/quote]");
            $post->setTopic($topic);
            $post->setName($this->get("translator")->trans("Re:", array(), "symbb_frontend")." ".$topic->getName());
            return $this->handlePost($request, $post);
        }

        $this->addError("Topic not found!", $request);
        return $this->returnToLastPage($request);
    }

    /**
     * @param Request $request
     * @param Topic $topic
     * @return mixed
     */
    public function handleTopic(Request $request, Topic $topic){

        $editReason = null;
        if($topic->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(TopicVoter::EDIT, $topic)) {
                throw $this->createAccessDeniedException();
            }
            $editReason = $request->get("editReason", "");
        } else {
            if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::CREATE_TOPIC, $topic->getForum())) {
                throw $this->createAccessDeniedException();
            }
        }

        $form = $this->createForm("topic", $topic, array("attr" => array("class" => "css-form form-horizontal")));

        $oldText = $topic->getMainPost()->getText();

        $form->handleRequest($request);

        if ($form->isValid()) {

            // insert edit history
            if($editReason !== null){
                $history = new Post\History();
                $history->setPost($topic->getMainPost());
                $history->setChanged(new \DateTime());
                $history->setEditor($this->getUser());
                $history->setOldText($oldText);
                $history->setReason($editReason);
                $topic->getMainPost()->addHistory($history);
            }

            $event = new TopicFormSaveEvent($topic, $request, $form);
            $this->get("event_dispatcher")->dispatch('symbb.core.forum.form.topic.before.save', $event);
            $this->get("symbb.core.topic.manager")->save($topic);
            $this->get("event_dispatcher")->dispatch('symbb.core.forum.form.topic.after.save', $event);

            if ($request->get("notifyMe", false)) {
                $this->get('symbb.core.topic.flag')->insertFlag($topic, 'notify');
            } else {
                $this->get('symbb.core.topic.flag')->removeFlag($topic, 'notify');
            }

            $this->get('symbb.core.post.flag')->insertFlags($topic->getMainPost(), 'new');

            return $this->redirect($this->generateUrl('symbb_forum_topic_show', array("id" => $topic->getId(), "name" => $topic->getSeoName(), "page" => 1)));
        }

        return $this->render($this->getTemplateBundleName('forum') . ':Forum:topicEdit.html.twig', array("topic" => $topic, "form" => $form->createView()));
    }


    /**
     * @param Request $request
     * @param Post $post
     * @return mixed
     */
    public function handlePost(Request $request, Post $post){

        $editReason = null;
        if($post->getId() > 0){
            if (!$this->get('security.authorization_checker')->isGranted(PostVoter::EDIT, $post)) {
                throw $this->createAccessDeniedException();
            }
            $editReason = $request->get("editReason", "");
        } else {
            if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::CREATE_POST, $post->getTopic()->getForum())) {
                throw $this->createAccessDeniedException();
            }
        }

        $form = $this->createForm("post", $post, array("attr" => array("class" => "css-form form-horizontal")));

        $oldText = $post->getText();

        $form->handleRequest($request);

        if ($form->isValid()) {

            // insert edit history
            if($editReason !== null){
                $history = new Post\History();
                $history->setPost($post);
                $history->setChanged(new \DateTime());
                $history->setEditor($this->getUser());
                $history->setOldText($oldText);
                $history->setReason($editReason);
                $post->addHistory($history);
            }

            $event = new PostFormSaveEvent($post, $request, $form);
            $this->get("event_dispatcher")->dispatch('symbb.core.forum.form.post.before.save', $event);
            $this->get("symbb.core.post.manager")->save($post);
            $this->get("event_dispatcher")->dispatch('symbb.core.forum.form.post.after.save', $event);
            $data = $request->get("post");

            if ($data["notifyMe"]) {
                $this->get('symbb.core.topic.flag')->insertFlag($post->getTopic(), 'notify');
            } else {
                $this->get('symbb.core.topic.flag')->removeFlag($post->getTopic(), 'notify');
            }

            $this->get('symbb.core.post.flag')->insertFlags($post, 'new');

            return $this->redirect($this->generateUrl('symbb_forum_topic_show', array("id" => $post->getTopic()->getId(), "name" => $post->getTopic()->getSeoName(), "page" => "last")));
        }

        return $this->render($this->getTemplateBundleName('forum') . ':Forum:postEdit.html.twig', array("post" => $post, "form" => $form->createView()));
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUploadAction(Request $request)
    {

        $id = (int)$request->get('id');
        $forum = (int)$request->get('forum');
        $forum = $this->get("symbb.core.forum.manager")->find($forum);

        $params = array();

        if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::UPLOAD_FILE, $forum)) {
            throw $this->createAccessDeniedException();
        }

        $files = $request->files;

        if (\is_object($files)) {
            $uploadManager = $this->get('symbb.core.upload.manager');
            $uploadSet = 'editor';
            $fileData = $uploadManager->handleUpload($request, $uploadSet);
            $fileData = reset($fileData);
            $params["link"] = $fileData["url"];
            $params["files"] = $fileData;
        } else {
            $params["error"] = "Error while uploading file.";
        }

        $response = new \Symfony\Component\HttpFoundation\Response(json_encode($params));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}