<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\TapatalkBundle\Manager;

use Symbb\Core\ForumBundle\Security\Authorization\PostVoter;
use Symbb\Core\ForumBundle\Security\Authorization\TopicVoter;

/**
 * http://tapatalk.com/api/api_section.php?id=4
 */
class PostManager extends AbstractManager
{

    public function getThread($topicId, $startNumber, $lastNumber)
    {
        $this->debug("getThread");
        $limit = 50;
        $page = 1;
        $this->calcLimitandPage($startNumber, $lastNumber, $limit, $page);
        
        $topic = $this->topicManager->find($topicId);
        $posts = $this->postManager->findByTopic($topic, $limit, $page);
        $forum = $topic->getForum();

        $replyAccess = $this->accessManager->isGranted(TopicVoter::REPLY, $topic);

        $configList = array(
            'total_post_num' => new \Zend\XmlRpc\Value\Integer(count($posts)),
            'forum_id' => new \Zend\XmlRpc\Value\String($forum->getId()),
            'forum_name' => new \Zend\XmlRpc\Value\Base64($forum->getName()),
            'topic_id' => new \Zend\XmlRpc\Value\String($topic->getId()),
            'topic_title' => new \Zend\XmlRpc\Value\Base64($topic->getName()),
            'prefix' => new \Zend\XmlRpc\Value\Base64(""),
            'is_subscribed' => new \Zend\XmlRpc\Value\Boolean(false),
            'can_subscribe' => new \Zend\XmlRpc\Value\Boolean(false),
            'is_poll' => new \Zend\XmlRpc\Value\Boolean(false),
            'is_closed' => new \Zend\XmlRpc\Value\Boolean(false),
            'can_report' => new \Zend\XmlRpc\Value\Boolean(false),
            'can_reply' => new \Zend\XmlRpc\Value\Boolean($replyAccess),
            'breadcrumb' => array(),
            'posts' => array(),
        );

        foreach ($posts as $post) {
            $author = $post->getAuthor();
            $configList["posts"][] = new \Zend\XmlRpc\Value\Struct(array(
                "post_id" => new \Zend\XmlRpc\Value\String($post->getId()),
                "post_title" => new \Zend\XmlRpc\Value\Base64($post->getName()),
                "post_content" => new \Zend\XmlRpc\Value\Base64($post->getText()),
                "post_author_id" => new \Zend\XmlRpc\Value\String($author->getId()),
                "post_author_name" => new \Zend\XmlRpc\Value\Base64($author->getUsername()),
                "is_online" => new \Zend\XmlRpc\Value\Boolean(false),
                "can_edit" => new \Zend\XmlRpc\Value\Boolean(false),
                "icon_url" => new \Zend\XmlRpc\Value\String($this->userManager->getAbsoluteAvatarUrl($author)),
                "post_time" => new \Zend\XmlRpc\Value\DateTime($post->getCreated()),
                "allow_smilies" => new \Zend\XmlRpc\Value\Boolean(false),
                "attachments" => array(),
                "thanks_info" => array(),
                "likes_info" => array(),
            ));
        }

        return $this->getResponse($configList, 'struct');
    }
}