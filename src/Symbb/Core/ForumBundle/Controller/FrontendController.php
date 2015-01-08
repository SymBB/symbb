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

}