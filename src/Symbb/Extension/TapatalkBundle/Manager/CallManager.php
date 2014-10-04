<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\TapatalkBundle\Manager;

class CallManager
{

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
    // ++++ Forum +++++ //

    /**
     * 
     * @return object
     */
    public function get_config()
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.forum');
            return $manager->getConfig();
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param boolean $returnDescription
     * @param integer $forumId
     * @return object
     */
    public function get_forum($returnDescription = true, $forumId = 0)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.forum');
            return $manager->getForum($returnDescription, $forumId);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @return object
     */
    public function get_participated_forum()
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.forum');
            return $manager->getParticipatedForum();
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param integer $forumId
     * @return object
     */
    public function mark_all_as_read($forumId = 0)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.forum');
            return $manager->markAllAsRead($forumId);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param integer $forumId
     * @param base64 $password
     * @return object
     */
    public function login_forum($forumId, $password)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.forum');
            return $manager->loginForum($forumId, $password);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @return object
     */
    public function get_board_stat()
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.forum');
            return $manager->getBoardStat();
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param array $forumIds
     * @return object
     */
    public function get_forum_status($forumIds)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.forum');
            return $manager->getForumStatus($forumIds);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @return object
     */
    public function get_smilies()
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.forum');
            return $manager->getSmilies();
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }
    // ++++ Topic +++++ //

    /**
     * 
     * @param array $topics
     * @return object
     */
    public function mark_topic_read($topics)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.topic');
            return $manager->markTopicRead($topics);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param type $topics
     * @return object
     */
    public function get_topic_status($topics)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.topic');
            return $manager->getTopicStatus($topics);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param string $forumId
     * @param base64 $subject
     * @param base64 $text
     * @param string $prefixId
     * @param array $attachmentIds
     * @param string $groupId
     * @return object
     */
    public function new_topic($forumId, $subject, $text, $prefixId = "", $attachmentIds = array(), $groupId = 0)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.topic');
            return $manager->newTopic($forumId, $subject, $text, $prefixId, $attachmentIds, $groupId);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param string $forumId
     * @param integer $startNumber
     * @param integer $lastNumber
     * @param string $mode
     * @return object
     */
    public function get_topic($forumId, $startNumber = null, $lastNumber = null, $mode = "")
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.topic');
            return $manager->getTopic($forumId, $startNumber, $lastNumber, $mode);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param integer $startNumber
     * @param integer $lastNumber
     * @param string $searchid
     * @param array $filters
     * @return object
     */
    public function get_unread_topic($startNumber = null, $lastNumber = null, $searchid = 0, $filters = array())
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.topic');
            return $manager->getUnreadTopic($startNumber, $lastNumber, $searchid, $filters);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    /**
     * 
     * @param base64 $username
     * @param integer $startNumber
     * @param integer $lastNumber
     * @param string $searchid
     * @param string $userId
     * @return object
     */
    public function get_participated_topic($username, $startNumber, $lastNumber, $searchid = 0, $userId)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.topic');
            return $manager->getParticipatedTopic($username, $startNumber, $lastNumber, $searchid, $userId);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }
    
    
    
    
    // ++++ Post +++++ //

    /**
     * 
     * @param string $forum_id
     * @param string $topic_id
     * @param base64 $subject
     * @param base64 $body
     * @return object
     */
    public function reply_post($forum_id, $topic_id, $subject, $body)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.post');
            return $manager->replyPost($forum_id, $topic_id, $subject, $body);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }
    
    /**
     * 
     * @param string $post_id
     * @return object
     */
    public function get_quote_post($post_id)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.post');
            return $manager->getQuotePost($post_id);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }
    
    /**
     * 
     * @param string $post_id
     * @return object
     */
    public function get_raw_post($post_id)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.post');
            return $manager->getRawPost($post_id);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }
    

    /**
     * 
     * @param string $post_id
     * @param base64 $post_title
     * @param base64 $post_content
     * @return object
     */
    public function save_raw_post($post_id, $post_title, $post_content)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.post');
            return $manager->saveRawPost($post_id, $post_title, $post_content);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }
    

    /**
     * 
     * @param string $topic_id
     * @param integer $startNum
     * @param integer $endNum
     * @return object
     */
    public function get_thread($topic_id, $startNum, $endNum)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.post');
            return $manager->getThread($topic_id, $startNum, $endNum);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }
    
    
    // ++++ User +++++ //

    /**
     * 
     * @param base64 $loginName
     * @param base64 $password
     * @return object
     */
    public function login($loginName, $password)
    {
        try {
            $manager = $this->container->get('symbb.extension.tapatalk.manager.user');
            $providerKey = $this->container->getParameter('fos_user.firewall_name');
            return $manager->login($loginName, $password, $this->container->get('request'), $providerKey);
        } catch (\Exception $exc) {
            return $this->errorResponse($exc);
        }
    }

    protected function errorResponse(\Exception $exc)
    {
        $this->container->get('logger')->error('Error in CallManager: ' . $exc->getMessage());
        $this->container->get('logger')->error($this->container->get('request'));
        throw new $exc;
    }
}
