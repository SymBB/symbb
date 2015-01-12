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
use Symbb\Core\ForumBundle\Form\Type\TopicType;
use Symbb\Core\ForumBundle\Security\Authorization\ForumVoter;
use Symbb\Core\ForumBundle\Security\Authorization\TopicVoter;
use Symfony\Component\HttpFoundation\Request;

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

    public function createTopicAction(Request $request){
        $forumId = $request->get("forum");
        $forum = $this->get('symbb.core.forum.manager')->find($forumId);

        if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::CREATE_TOPIC, $forum)) {
            throw $this->createAccessDeniedException();
        }

        $topic = new Topic();
        $post = new Post();
        $topic->setForum($forum);
        $topic->setMainPost($post);
        $form = $this->createForm("topic", $topic, array("attr" => array("class" => "css-form form-horizontal")));
        return $this->render($this->getTemplateBundleName('forum') . ':Forum:topicEdit.html.twig', array("topic" => $topic, "form" => $form->createView()));
    }

    public function saveTopic(){

    }


    public function postUploadAction(Request $request)
    {

        $id = (int)$request->get('id');
        $forum = (int)$request->get('forum');
        $forum = $this->get("symbb.core.forum.manager")->find($forum);

        $params = array();

        //if (!$this->get('security.authorization_checker')->isGranted(ForumVoter::UPLOAD_FILE, $forum)) {
        //    throw $this->createAccessDeniedException();
        //}

        $files = $request->files;

        if (\is_object($files)) {
            $uploadManager = $this->get('symbb.core.upload.manager');
            $uploadSet = 'tmp';
            if ($id > 0) {
                $uploadSet = 'post';
            }
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