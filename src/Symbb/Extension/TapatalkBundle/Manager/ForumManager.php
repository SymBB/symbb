<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\TapatalkBundle\Manager;

/**
 * http://tapatalk.com/api/api_section.php?id=1
 */
class ForumManager extends AbstractManager
{

    public function getConfig()
    {

        $configList = array(
            //'sys_version' => new \Zend\XmlRpc\Value\String('1.0.0'),
            'version' => new \Zend\XmlRpc\Value\String('1'),
            'api_level' => new \Zend\XmlRpc\Value\String('3'),
            'is_open' => new \Zend\XmlRpc\Value\Boolean(true),
            'guest_okay' => new \Zend\XmlRpc\Value\Boolean(true),
            'report_post' => new \Zend\XmlRpc\Value\String('false'),
            'report_pm' => new \Zend\XmlRpc\Value\String('false'),
            'mark_read' => new \Zend\XmlRpc\Value\String('0'),
            'mark_forum' => new \Zend\XmlRpc\Value\String('0'),
            'subscribe_forum' => new \Zend\XmlRpc\Value\String('0'),
            'delete_reason' => new \Zend\XmlRpc\Value\String('0'),
            'm_approve' => new \Zend\XmlRpc\Value\String('0'),
            'm_delete' => new \Zend\XmlRpc\Value\String('0'),
            'm_report' => new \Zend\XmlRpc\Value\String('0'),
            'anonymous' => new \Zend\XmlRpc\Value\String('1'),
            'delete_reason' => new \Zend\XmlRpc\Value\String('0'),
            'alert' => new \Zend\XmlRpc\Value\String('1'),
            'guest_search' => new \Zend\XmlRpc\Value\Boolean(false),
            'guest_whosonline' => new \Zend\XmlRpc\Value\Boolean(false),
            'require_activation' => new \Zend\XmlRpc\Value\Boolean(false),
            'support_md5' => new \Zend\XmlRpc\Value\String(0),
            'support_sha1' => new \Zend\XmlRpc\Value\String(0),
            'tapatalk_push_key' => new \Zend\XmlRpc\Value\String("4BB5A17F3DCD0426EF82C8A48CEC079C"),
            'automod' => new \Zend\XmlRpc\Value\String('1'),
            'api_key' => new \Zend\XmlRpc\Value\String(md5("4BB5A17F3DCD0426EF82C8A48CEC079C")),
            'avatar' => new \Zend\XmlRpc\Value\String('1'),
            'get_id_by_url' => new \Zend\XmlRpc\Value\String('0'),
        );

        $configList['stats'] = $this->getConfigStats();

        return $this->getResponse($configList, 'struct');
    }

    public function getParticipatedForum()
    {
        $data = array();
        return $this->getResponse($data, 'struct');
    }

    public function markAllAsRead($forumId = 0)
    {
        $success = false;
        if($forumId > 0){
            $forum =$this->forumManager->find($forumId);
            if($forum){
                $success = $this->forumManager->markAsRead($forum);
            }
        } else {
            $success = $this->forumManager->markAllAsRead();
        }

        $data = array(
            'result' => new \Zend\XmlRpc\Value\Boolean($success)
        );
        return $this->getResponse($data, 'struct');
    }

    public function loginForum($forumId, $password)
    {
        $data = array(
            'result' => new \Zend\XmlRpc\Value\Boolean(false)
        );
        return $this->getResponse($data, 'struct');
    }

    public function getForumStatus($forumIds)
    {
        $data = array();
        return $this->getResponse($data, 'struct');
    }

    public function getSmilies()
    {
        $data = array();
        return $this->getResponse($data, 'struct');
    }

    public function getBoardStat()
    {
        $userCount = $this->userManager->countUsers();
        $topicCount = $this->forumManager->countTopics();
        $postCount = $this->forumManager->countTopics();

        $data = array(
            'total_threads' => new \Zend\XmlRpc\Value\Integer($topicCount),
            'total_members' => new \Zend\XmlRpc\Value\Integer($userCount),
            'total_posts' => new \Zend\XmlRpc\Value\Integer($postCount),
            'active_members' => new \Zend\XmlRpc\Value\Integer($userCount),
            'total_online' => new \Zend\XmlRpc\Value\Integer(0),
            'guest_online' => new \Zend\XmlRpc\Value\Integer(0),
        );

        return $this->getResponse($data, 'struct');
    }

    /**
     * 
     * @return \Zend\XmlRpc\Value\Struct
     */
    protected function getConfigStats()
    {
        $userCount = $this->userManager->countUsers();
        $topicCount = $this->forumManager->countTopics();
        $postCount = $this->forumManager->countTopics();

        $data = new \Zend\XmlRpc\Value\Struct(array(
            'topic' => new \Zend\XmlRpc\Value\Integer($topicCount),
            'user' => new \Zend\XmlRpc\Value\Integer($userCount),
            'post' => new \Zend\XmlRpc\Value\Integer($postCount),
            'active' => new \Zend\XmlRpc\Value\Integer(0),
        ));

        return $data;
    }

    public function getForum($returnDescription = false, $forumId = 0)
    {
        $forums = array();
        $forumData = array();

        if ($forumId <= 0) {
            $forums = $this->forumManager->findAll(0);
        } else {
            $forums[] = $this->forumManager->find($forumId);
        }

        foreach ($forums as $forum) {
            if (\is_object($forum)) {
                $forumData[] = $this->getForumData($forum);
            }
        }

        return $this->getResponse($forumData, "array");
    }

    protected function getForumData($forum)
    {
        $forumData = null;

        $this->accessManager->addAccessCheck('SYMBB_FORUM#VIEW', $forum);
        if ($this->accessManager->hasAccess()) {
            $parent = $forum->getParent();
            $parentId = 0;
            if (\is_object($parent)) {
                $parentId = $parent->getId();
            }
            $subOnly = false;
            if ($forum->getType() !== 'forum') {
                $subOnly = true;
            }
            $childs = $forum->getChildren();
            $childData = array();
            foreach ($childs as $child) {
                $forumChildData = $this->getForumData($child);
                if ($forumChildData) {
                    $childData[] = $forumChildData;
                }
            }

            $forumData = new \Zend\XmlRpc\Value\Struct(array(
                'forum_id' => new \Zend\XmlRpc\Value\String($forum->getId()),
                'forum_name' => new \Zend\XmlRpc\Value\Base64($forum->getName()),
                'description' => new \Zend\XmlRpc\Value\Base64($forum->getDescription()),
                'parent_id' => new \Zend\XmlRpc\Value\String($parentId),
                'is_protected' => new \Zend\XmlRpc\Value\Boolean(false),
                'is_subscribed' => new \Zend\XmlRpc\Value\Boolean(false),
                'can_subscribe' => new \Zend\XmlRpc\Value\Boolean(false),
                'url' => new \Zend\XmlRpc\Value\String($forum->getLink()),
                'sub_only' => new \Zend\XmlRpc\Value\Boolean($subOnly),
                'child' => $childData
            ));
        }

        return $forumData;
    }
}