<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\TapatalkBundle\Manager;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

/**
 * http://tapatalk.com/api/api_section.php?id=2
 */
class UserManager extends AbstractManager
{

    public function login($username, $password, Request $request, $providerKey)
    {


        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');
        $result = array();
        $this->logger->debug('Tapatalk Login: user: '.$username);
        $userLoggedIn = $this->userManager->login($username, $password, $request, $providerKey, $response);

        $user = $this->userManager->getCurrentUser();
        $groups = $user->getGroups();
        $groupIds = array();
        foreach ($groups as $group) {
            $groupIds[] = new \Zend\XmlRpc\Value\String($group->getId());
        }

        if (!$userLoggedIn) {
            $loginMessage = 'Wrong Login';
            $result['status'] = new \Zend\XmlRpc\Value\String('2');
            $result['result_text'] = new \Zend\XmlRpc\Value\Base64($loginMessage);
        }

        $result['result'] = new \Zend\XmlRpc\Value\Boolean($userLoggedIn);
        $result['user_id'] = new \Zend\XmlRpc\Value\String($user->getId());
        $result['login_name'] = new \Zend\XmlRpc\Value\Base64($user->getUsername());
        $result['username'] = new \Zend\XmlRpc\Value\Base64($user->getUsername());
        //$result['usergroup_id'] = new \Zend\XmlRpc\Value\Struct($groupIds);
        $result['email'] = new \Zend\XmlRpc\Value\Base64($user->getEmail());
        $result['icon_url'] = new \Zend\XmlRpc\Value\String($this->userManager->getAbsoluteAvatarUrl());
        $result['post_count'] = new \Zend\XmlRpc\Value\Integer($user->getPosts()->count());
        $result['user_type'] = new \Zend\XmlRpc\Value\Base64('normal');
        $result['can_pm'] = new \Zend\XmlRpc\Value\Boolean(true);
        $result['can_send_pm'] = new \Zend\XmlRpc\Value\Boolean(true);
        $result['can_moderate'] = new \Zend\XmlRpc\Value\Boolean(false);
        $result['can_upload_avatar'] = new \Zend\XmlRpc\Value\Boolean(false);

        $response2 = $this->getResponse($result, 'struct');
        $response->setContent($response2->getContent());

        return $response;
    }

    public function getInboxStat(){
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        $topics = $this->topicManager->getFlagHandler()->findFlagsByClassAndFlag('Symbb\Core\Forum\Entity\Topic', 'new');
        $messages = $this->messageManager->countNewMessages();

        $result['inbox_unread_count'] = new \Zend\XmlRpc\Value\Integer($messages);
        $result['subscribed_topic_unread_count'] = new \Zend\XmlRpc\Value\Integer(count($topics));

        $response2 = $this->getResponse($result, 'struct');
        $response->setContent($response2->getContent());

        return $response;
    }
}