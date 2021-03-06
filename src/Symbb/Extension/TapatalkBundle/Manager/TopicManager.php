<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\TapatalkBundle\Manager;

use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\ForumBundle\Security\Authorization\ForumVoter;
use Symbb\Core\SystemBundle\Manager\AbstractFlagHandler;

/**
 * http://tapatalk.com/api/api_section.php?id=3
 */
class TopicManager extends AbstractManager
{

    public function markTopicRead($topicIds)
    {
        $this->debug("markTopicRead");
        $success = true;
        foreach ($topicIds as $topicId) {
            $topic = $this->topicManager->find($topicId);
            $access = $this->securityContext->isGranted(ForumVoter::VIEW, $topic->getForum());
            if ($access) {
                if ($topic) {
                    $this->topicManager->markAsRead($topic);
                }
            } else {
                $success = false;
            }
        }
        $configList = array(
            'result' => new \Zend\XmlRpc\Value\Boolean($success),
            'result_text' => new \Zend\XmlRpc\Value\Base64(""),
        );

        return $this->getResponse($configList, 'struct');
    }

    public function getTopicStatus($topicIds)
    {
        $this->debug("getTopicStatus");

        $data = array();
        foreach ($topicIds as $topicId) {
            $topic = $this->topicManager->find($topicId);
            $access = $this->securityContext->isGranted(ForumVoter::VIEW, $topic->getForum());
            if ($access && $topic) {
                $ignored = $this->forumManager->isIgnored($topic->getForum());
                $datetime = $topic->getLastPost()->getCreated();
                $datetime->setTimezone($this->userManager->getTimezone());
                $datetimeString = $datetime->format(\DateTime::ISO8601);
                $data = array(
                    'topic_id' => $topic->getId(),
                    'is_subscribed' => !$ignored,
                    'can_subscribe' => true,
                    'is_closed' => $topic->isLocked(),
                    'last_reply_time' => $datetimeString,
                    'new_post' => $this->topicManager->checkFlag($topic, AbstractFlagHandler::FLAG_NEW),
                    'reply_number' => $topic->getPostCount(),
                    'view_number' => 0
                );
            }
        }

        $configList = array(
            'result' => new \Zend\XmlRpc\Value\Boolean(false),
            'result_text' => new \Zend\XmlRpc\Value\Base64(""),
            'status' => $data,
        );

        return $this->getResponse($configList, 'struct');
    }

    public function newTopic($forumId, $subject, $text, $prefixId = "", $attachmentIds = array(), $groupId = 0)
    {
        $this->debug("newTopic");

        $forum = $this->forumManager->find($forumId);
        $access = $this->securityContext->isGranted(ForumVoter::CREATE_TOPIC, $forum);
        $success = false;
        $topicId = 0;
        if ($access) {
            if ($forum) {
                $topic = new Topic();
                $topic->setAuthor($this->userManager->getCurrentUser());
                $topic->setName($subject);
                $topic->setForum($forum);

                $post = new Post();
                $post->setName($subject);
                $post->setAuthor($this->userManager->getCurrentUser());
                $post->setText($text);
                $post->setTopic($topic);
                $topic->setMainPost($post);

                $success = $this->topicManager->save($topic);
                $topicId = $topic->getId();
            }
        } else {
            $this->debug("no access");
            $error = "no access";
            $success = false;
        }
        $configList = array(
            'result' => new \Zend\XmlRpc\Value\Boolean($success),
            'result_text' => new \Zend\XmlRpc\Value\Base64($error),
            'topic_id' => new \Zend\XmlRpc\Value\String($topicId),
            'state' => new \Zend\XmlRpc\Value\Integer(0),
        );

        return $this->getResponse($configList, 'struct');
    }

    public function getTopic($forumId, $startNumber = null, $lastNumber = null, $mode = "")
    {
        $this->debug("getTopic");

        $forum = $this->forumManager->find($forumId);
        $viewAccess = $this->securityContext->isGranted(ForumVoter::VIEW, $forum);
        $writeAccess = $this->securityContext->isGranted(ForumVoter::CREATE_TOPIC, $forum);

        $configList = array(
            'total_topic_num' => new \Zend\XmlRpc\Value\Integer(0),
            'forum_id' => new \Zend\XmlRpc\Value\String(0),
            'forum_name' => new \Zend\XmlRpc\Value\Base64(""),
            'can_post' => new \Zend\XmlRpc\Value\Boolean($writeAccess),
            'unread_sticky_count' => new \Zend\XmlRpc\Value\Integer(0),
            'unread_announce_count' => new \Zend\XmlRpc\Value\Integer(0),
            'can_subscribe' => new \Zend\XmlRpc\Value\Boolean(false),
            'is_subscribed' => new \Zend\XmlRpc\Value\Boolean(false),
            'require_prefix' => new \Zend\XmlRpc\Value\String(""),
            'prefixes' => array(),
            'prefix_id' => new \Zend\XmlRpc\Value\String(""),
            'prefix_display_name' => new \Zend\XmlRpc\Value\Base64(""),
            'topics' => array(),
        );

        if ($viewAccess && !in_array(strtoupper($mode), array('TOP', 'ANN'))) {
            $page = 1;
            $limit = 20;
            $this->calcLimitandPage($startNumber, $lastNumber, $limit, $page);

            $topics = $this->forumManager->findTopics($forum, $page, $limit);
            $this->debug('getTopic: count -> ' . count($topics));
            $this->debug('getTopic: page -> ' . $page . ', limit -> ' . $limit);

            $configList['total_topic_num'] = new \Zend\XmlRpc\Value\Integer($forum->getTopicCount());
            $configList['forum_id'] = new \Zend\XmlRpc\Value\String($forum->getId());
            $configList['forum_name'] = new \Zend\XmlRpc\Value\Base64($forum->getName());
            $configList['can_post'] = new \Zend\XmlRpc\Value\Boolean($writeAccess);

            foreach ($topics as $topic) {

                $closed = $topic->isLocked();
                $author = $topic->getAuthor();
                $lastPost = $topic->getLastPost();

                $new = $this->topicManager->getFlagHandler()->checkFlag($topic, "new");

                $content = $this->createShortContent($lastPost->getText());

                $configList['topics'][] = new \Zend\XmlRpc\Value\Struct(
                    array(
                        'forum_id' => new \Zend\XmlRpc\Value\String($forum->getId()),
                        'topic_id' => new \Zend\XmlRpc\Value\String($topic->getId()),
                        'topic_title' => new \Zend\XmlRpc\Value\Base64($topic->getName()),
                        'prefix' => new \Zend\XmlRpc\Value\Base64(""),
                        'topic_author_id' => new \Zend\XmlRpc\Value\String($author->getId()),
                        'topic_author_name' => new \Zend\XmlRpc\Value\Base64($author->getUsername()),
                        'is_subscribed' => new \Zend\XmlRpc\Value\Boolean(false),
                        'can_subscribe' => new \Zend\XmlRpc\Value\Boolean(false),
                        'is_closed' => new \Zend\XmlRpc\Value\Boolean($closed),
                        'icon_url' => new \Zend\XmlRpc\Value\String($this->userManager->getAbsoluteAvatarUrl($author)),
                        'last_reply_time' => new \Zend\XmlRpc\Value\DateTime($lastPost->getCreated()),
                        'reply_number' => new \Zend\XmlRpc\Value\Integer($topic->getPostCount()),
                        'new_post' => new \Zend\XmlRpc\Value\Boolean($new),
                        'view_number' => new \Zend\XmlRpc\Value\Integer(0),
                        'short_content' => new \Zend\XmlRpc\Value\Base64($content),
                        'participated_uids' => array()
                    )
                );
            }

        }

        return $this->getResponse($configList, 'struct');
    }

    public function getLatestTopics($startNumber = null, $lastNumber = null, $searchid = 0, $filters = array())
    {
        $this->debug("getLatestTopics");

        $limit = 50;
        $page = 1;
        $this->calcLimitAndPage($startNumber, $lastNumber, $limit, $page);

        $pagination = $this->postManager->search($page, $limit);
        $this->debug('getParticipatedTopic: $startNumber: ' . $startNumber . ' , $lastNumber: ' . $lastNumber);
        $this->debug('getParticipatedTopic: page: ' . $page . ' , limit: ' . $limit);
        $this->debug('getParticipatedTopic: count: ' . count($pagination));

        $configList = array(
            'result' => new \Zend\XmlRpc\Value\Boolean(true),
            'result_text' => new \Zend\XmlRpc\Value\Base64(""),
            'total_topic_num' => new \Zend\XmlRpc\Value\Integer(count($pagination)),
            'topics' => array()
        );

        foreach ($pagination as $post) {
            $topic = $post->getTopic();
            $configList["topics"][] = $this->getTopicAsStruct($topic);
        }

        return $this->getResponse($configList, 'struct');
    }

    protected function getTopicAsStruct(Topic $topic)
    {
        $forum = $topic->getForum();
        $author = $topic->getAuthor();
        $closed = $topic->isLocked();
        $new = $this->topicManager->checkFlag($topic, "new");
        return new \Zend\XmlRpc\Value\Struct(
            array(
                'forum_id' => new \Zend\XmlRpc\Value\String($forum->getId()),
                'forum_name' => new \Zend\XmlRpc\Value\Base64($forum->getName()),
                'topic_id' => new \Zend\XmlRpc\Value\String($topic->getId()),
                'topic_title' => new \Zend\XmlRpc\Value\Base64($topic->getName()),
                'prefix' => new \Zend\XmlRpc\Value\Base64(""),
                'post_author_id' => new \Zend\XmlRpc\Value\String($author->getId()),
                'post_author_name' => new \Zend\XmlRpc\Value\Base64($author->getUsername()),
                'is_subscribed' => new \Zend\XmlRpc\Value\Boolean(false),
                'can_subscribe' => new \Zend\XmlRpc\Value\Boolean(false),
                'is_closed' => new \Zend\XmlRpc\Value\Boolean($closed),
                'icon_url' => new \Zend\XmlRpc\Value\String($this->userManager->getAbsoluteAvatarUrl($author)),
                'post_time' => new \Zend\XmlRpc\Value\DateTime($topic->getCreated()),
                'reply_number' => new \Zend\XmlRpc\Value\Integer($topic->getPostCount()),
                'new_post' => new \Zend\XmlRpc\Value\Boolean($new),
                'view_number' => new \Zend\XmlRpc\Value\Integer(0),
                'short_content' => new \Zend\XmlRpc\Value\Base64("")
            )
        );
    }

    public function getParticipatedTopic($username, $startNumber, $lastNumber)
    {
        $this->debug("getParticipatedTopic");

        $user = $this->userManager->findByUsername($username);

        $topicData = array();

        $success = true;
        $error = "";
        $count = 0;
        $unread = 0;

        if ($user) {
            $limit = 50;
            $page = 1;
            $this->calcLimitandPage($startNumber, $lastNumber, $limit, $page);
            $topics = $this->topicManager->getParticipatedTopics($page, $limit, $user);
            $count = count($topics);
            foreach ($topics as $key => $topic) {
                $topicData[] = $this->getTopicAsStruct($topic);
            }
        } else {
            $error = "User not found";
            $success = false;
        }

        $configList = array(
            'result' => new \Zend\XmlRpc\Value\Boolean($success),
            'result_text' => new \Zend\XmlRpc\Value\Base64($error),
            'search_id' => new \Zend\XmlRpc\Value\String(0),
            'total_topic_num' => new \Zend\XmlRpc\Value\Integer($count),
            'total_unread_num' => new \Zend\XmlRpc\Value\Integer($unread),
            'topics' => $topicData,
        );

        return $this->getResponse($configList, 'struct');
    }
}