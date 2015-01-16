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
        $this->debug("login");
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        $result = array();
        $this->debug('user: ' . $username);
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

        $response = $this->getResponse($result, 'struct', true);

        $response->headers->set();

        return $response;
    }

    public function getInboxStat()
    {
        $this->debug('getInboxStat');

        $topics = $this->topicManager->getFlagHandler()->findFlagsByClassAndFlag('Symbb\Core\Forum\Entity\Topic', 'new');
        $messages = $this->messageManager->countNewMessages();

        $result['inbox_unread_count'] = new \Zend\XmlRpc\Value\Integer($messages);
        $result['subscribed_topic_unread_count'] = new \Zend\XmlRpc\Value\Integer(count($topics));

        $response = $this->getResponse($result, 'struct');

        return $response;
    }

    /**
     * https://tapatalk.com/api/api_section.php?id=7#create_message
     * @param $userName
     * @param $subject
     * @param $textBody
     * @param $action
     * @param $pmId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createMessage($userName, $subject, $textBody, $action, $pmId)
    {

        $this->debug('createMessage');
        $receivers = array();
        $success = true;
        $error = "";

        if ($action == 1) {
            $oldPm = $this->messageManager->find($pmId);
            $userName[] = $oldPm->getSender()->getUsername();
            $userName = array_unique($userName);
        } else if ($action == 2) {
            $oldPm = $this->messageManager->find($pmId);
            $textBody .= "[quote]" . $oldPm->getMessage() . "[/quote]";
        }

        foreach ($userName as $currUserName) {
            $user = $this->userManager->findByUsername($currUserName);
            if ($user) {
                $receivers[$user->getId()] = $user;
            } else {
                $success = false;
                $error = "User not found";
                break;
            }
        }

        $id = 0;

        if ($success) {
            $errors = array();
            $this->debug("Tapatalk: sendMessage");
            $message = $this->messageManager->sendMessage($subject, $textBody, $receivers, $errors);

            if (!empty($errors)) {
                $error = implode(', ', $errors);
                $success = false;
            } else {
                $id = $message->getId();
            }
        }

        $result['result'] = new \Zend\XmlRpc\Value\Boolean($success);
        if (!empty($error)) {
            $result['result_text'] = new \Zend\XmlRpc\Value\Base64($error);
        }
        $result['msg_id'] = new \Zend\XmlRpc\Value\String($id);

        $response = $this->getResponse($result, 'struct');

        return $response;
    }

    /**
     * https://tapatalk.com/api/api_section.php?id=7#get_box_info
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getBoxInfo()
    {
        $this->debug('getBoxInfo');

        $boxInbox = new \Zend\XmlRpc\Value\Struct(array(
            'box_id' => new \Zend\XmlRpc\Value\String('inbox'),
            'box_name' => new \Zend\XmlRpc\Value\Base64("Inbox"),
            'msg_count' => new \Zend\XmlRpc\Value\Integer($this->messageManager->countMessages()),
            'unread_count' => new \Zend\XmlRpc\Value\Integer($this->messageManager->countNewMessages()),
            'box_type' => new \Zend\XmlRpc\Value\String("INBOX")
        ));

        $boxSent = new \Zend\XmlRpc\Value\Struct(array(
            'box_id' => new \Zend\XmlRpc\Value\String('sent'),
            'box_name' => new \Zend\XmlRpc\Value\Base64("Sent"),
            'msg_count' => new \Zend\XmlRpc\Value\Integer($this->messageManager->findSentMessages()->count()),
            'unread_count' => new \Zend\XmlRpc\Value\Integer(0),
            'box_type' => new \Zend\XmlRpc\Value\String("SENT")
        ));

        $result['result'] = new \Zend\XmlRpc\Value\Boolean(true);
        $result['list'] = array(
            $boxInbox,
            $boxSent
        );

        $response = $this->getResponse($result, 'struct');
        return $response;
    }

    /**
     * https://tapatalk.com/api/api_section.php?id=7#get_box
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getBox($boxId, $startNum = null, $endNum = null)
    {

        $this->debug('getBox');
        $page = 1;
        $limit = 20;

        if ($startNum && $endNum) {
            $this->calcLimitandPage($startNum, $endNum, $limit, $page);
        }

        if ($boxId == "inbox") {
            $messages = $this->messageManager->findReceivedMessages(null, $page, $limit);
            $newMessageCount = $this->messageManager->countNewMessages();
        } else if ($boxId == "sent") {
            $messages = $this->messageManager->findSentMessages(null, $page, $limit);
            $newMessageCount = 0;
        } else if ($boxId == "unread") {
            $messages = $this->messageManager->findReceivedMessages(null, $page, $limit, true);
            $newMessageCount = count($messages);
        }

        $result['result'] = new \Zend\XmlRpc\Value\Boolean(true);
        $result['total_message_count'] = new \Zend\XmlRpc\Value\Integer(count($messages));
        $result['total_message_count'] = new \Zend\XmlRpc\Value\Integer($newMessageCount);


        $result['list'] = array();
        foreach ($messages as $message) {

            $state = 2;
            $msgTo = array();
            foreach ($message->getReceivers() as $reciver) {
                if ($reciver->getUser()->getId() == $this->userManager->getCurrentUser()->getId()) {
                    if ($reciver->getNew()) {
                        $state = 1;
                    }
                }
                $msgTo[] = new \Zend\XmlRpc\Value\Struct(array(
                    'user_id' => new \Zend\XmlRpc\Value\String($reciver->getUser()->getId()),
                    'username' => new \Zend\XmlRpc\Value\Base64($reciver->getUser()->getUsername())
                ));
            }

            $result['list'][] = new \Zend\XmlRpc\Value\Struct(array(
                'msg_id' => new \Zend\XmlRpc\Value\String($message->getId()),
                'msg_state' => new \Zend\XmlRpc\Value\Integer($state),
                'sent_date' => new \Zend\XmlRpc\Value\DateTime($message->getDate()),
                'msg_from_id' => new \Zend\XmlRpc\Value\String($message->getSender()->getId()),
                'msg_from' => new \Zend\XmlRpc\Value\Base64($message->getSender()->getUsername()),
                'icon_url' => new \Zend\XmlRpc\Value\String($this->userManager->getAbsoluteAvatarUrl($message->getSender())),
                'msg_subject' => new \Zend\XmlRpc\Value\Base64($message->getSubject()),
                'short_content' => new \Zend\XmlRpc\Value\Base64($this->createShortContent($message->getMessage())),
                'msg_to' => $msgTo
            ));
        }

        $response = $this->getResponse($result, 'struct');

        return $response;
    }

    /**
     * https://tapatalk.com/api/api_section.php?id=7#get_message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMessage($messageId, $boxId)
    {

        $this->debug('getMessage');
        $message = $this->messageManager->find($messageId);
        $text = $message->getMessage();

        $msgTo = array();
        foreach ($message->getReceivers() as $reciver) {
            $msgTo[] = new \Zend\XmlRpc\Value\Struct(array(
                'user_id' => new \Zend\XmlRpc\Value\String($reciver->getUser()->getId()),
                'username' => new \Zend\XmlRpc\Value\Base64($reciver->getUser()->getUsername())
            ));
        }


        $result['result'] = new \Zend\XmlRpc\Value\Boolean(true);
        $result['msg_from_id'] = new \Zend\XmlRpc\Value\String($message->getSender()->getId());
        $result['msg_from'] = new \Zend\XmlRpc\Value\Base64($message->getSender()->getUsername());
        $result['icon_url'] = new \Zend\XmlRpc\Value\String($this->userManager->getAbsoluteAvatarUrl($message->getSender()));
        $result['sent_date'] = new \Zend\XmlRpc\Value\DateTime($message->getDate());
        $result['msg_subject'] = new \Zend\XmlRpc\Value\Base64($message->getSubject());
        $result['text_body'] = new \Zend\XmlRpc\Value\Base64($text);
        $result['msg_to'] = $msgTo;

        $response = $this->getResponse($result, 'struct');

        return $response;
    }

    /**
     *
     * https://tapatalk.com/api/api_section.php?id=7#get_quote_pm
     */
    public function getQuotePm($messageId)
    {

        $this->debug('getQuotePm');
        $message = $this->messageManager->find($messageId);
        $text = $message->getMessage();
        $text = '[quote]' . $text . '[/quote]';

        $result['result'] = new \Zend\XmlRpc\Value\Boolean(true);
        $result['msg_id'] = new \Zend\XmlRpc\Value\String($message->getId());
        $result['msg_subject'] = new \Zend\XmlRpc\Value\Base64($message->getSubject());
        $result['text_body'] = new \Zend\XmlRpc\Value\Base64($text);

        $response = $this->getResponse($result, 'struct');
        return $response;
    }

    /**
     * https://tapatalk.com/api/api_section.php?id=7#delete_message
     * @param $messageId
     * @param $boxId
     */
    public function deleteMessage($messageId, $boxId)
    {
        $this->debug('$messageId');
        $message = $this->messageManager->find($messageId);
        $success = $this->messageManager->remove($message);

        $result['result'] = new \Zend\XmlRpc\Value\Boolean($success);
        $response = $this->getResponse($result, 'struct');

        return $response;
    }


    /**
     * https://tapatalk.com/api/api_section.php?id=7#mark_pm_unread
     * @param $messageId
     */
    public function markPmUnread($messageId)
    {
        $this->debug('markPmUnread');
        $message = $this->messageManager->find($messageId);

        foreach ($message->getReceivers() as $reciver) {
            if ($reciver->getUser()->getId() == $this->userManager->getCurrentUser()->getId()) {
                $this->messageManager->unread($reciver);
            }
        }

        $result['result'] = new \Zend\XmlRpc\Value\Boolean(true);
        $response = $this->getResponse($result, 'struct');

        return $response;
    }

    /**
     * https://tapatalk.com/api/api_section.php?id=7#mark_pm_unread
     * @param $messageId
     */
    public function markPmRead($messageIds)
    {
        $this->debug('markPmRead');
        $messageIds = explode(',', $messageIds);

        foreach ($messageIds as $messageId) {
            $message = $this->messageManager->find($messageId);

            foreach ($message->getReceivers() as $reciver) {
                if ($reciver->getUser()->getId() == $this->userManager->getCurrentUser()->getId()) {
                    $this->messageManager->read($reciver);
                }
            }
        }

        $result['result'] = new \Zend\XmlRpc\Value\Boolean(true);
        $response = $this->getResponse($result, 'struct');

        return $response;
    }
}