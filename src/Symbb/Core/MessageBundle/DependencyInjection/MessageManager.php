<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\MessageBundle\DependencyInjection;

use Symbb\Core\MessageBundle\Entity\Message;
use Symbb\Core\MessageBundle\Entity\Message\Receiver;
use Symbb\Core\MessageBundle\Event\ParseMessageEvent;
use Symbb\Core\SystemBundle\Manager\AbstractManager;
use Symbb\Core\UserBundle\Entity\UserInterface;

class MessageManager extends AbstractManager
{


    const ERROR_RECEIVER_NOT_FOUND = 'receiver not found';
    const ERROR_SUBJECT_EMPTY = 'subject is empty';
    const ERROR_MESSAGE_EMPTY = 'message is empty';
    const ERROR_NOT_ALLOWED = 'action not allowed';

    /**
     * @param $id
     * @return Message
     */
    public function find($id)
    {

        $cacheKey = implode("_", array("find", $id));
        $message = $this->getCacheData($cacheKey);
        if ($message === null) {
            $message = $this->em->getRepository('SymbbCoreMessageBundle:Message')->find($id);
            $this->setCacheData($cacheKey, $message);
        }
        return $message;
    }

    /**
     * @param $subject
     * @param $messageText
     * @param $receivers
     * @param UserInterface $sender
     * @return array
     */
    public function sendMessage($subject, $messageText, $receivers, &$errors, UserInterface $sender = null)
    {

        //todo event beforSend

        if (!$sender) {
            $sender = $this->getUser();
        }

        if ($sender->getSymbbType() !== 'user') {
            $errors[] = self::ERROR_NOT_ALLOWED;
        }

        if (empty($subject)) {
            $errors[] = self::ERROR_SUBJECT_EMPTY;
        }
        if (empty($messageText)) {
            $errors[] = self::ERROR_MESSAGE_EMPTY;
        }

        $message = new Message();
        $message->setSubject($subject);
        $message->setMessage($messageText);
        $message->setSender($sender);
        foreach ($receivers as $receiver) {
            if ($receiver instanceof UserInterface) {
                $receiverObject = new Receiver();
                $receiverObject->setUser($receiver);
                $receiverObject->setMessage($message);
                $message->addReceiver($receiverObject);
            } else {
                $errors[] = self::ERROR_RECEIVER_NOT_FOUND;
            }
        }

        if (empty($errors)) {
            // "send" means in first step saving into database, notify user if option is actived,...
            $this->em->persist($message);
            $this->em->flush();
        }

        //todo event afterSend
        return $message;
    }

    /**
     * @param UserInterface $sender
     * @param int $page
     * @param int $limit
     * @return Message[]
     */
    public function findSentMessages(UserInterface $sender = null, $page = 1, $limit = 20)
    {

        if (!$sender) {
            $sender = $this->getUser();
        }

        $cacheKey = implode("_", array("findSentMessages", $sender->getId(), $page, $limit));
        $pagination = $this->getCacheData($cacheKey);

        if ($pagination === null) {
            $sql = "SELECT
                    m
                FROM
                    SymbbCoreMessageBundle:Message m
                WHERE
                  m.sender = ?1
                ORDER BY
                  m.date DESC";

            $query = $this->em->createQuery($sql);
            $query->setParameter(1, $sender->getId());

            $pagination = $this->createPagination($query, $page, $limit);
            $this->setCacheData($cacheKey, $pagination);
        }

        return $pagination;
    }

    /**
     * @param UserInterface $receiver
     * @param int $page
     * @param int $limit
     * @return Message[]
     */
    public function findReceivedMessages(UserInterface $receiver = null, $page = 1, $limit = 20, $new = null)
    {

        if (!$receiver) {
            $receiver = $this->getUser();
        }

        $cacheKey = implode("_", array("findReceivedMessages", $receiver->getId(), $page, $limit, $new));
        $pagination = $this->getCacheData($cacheKey);

        if ($pagination === null) {
            $where = '';
            if ($new === true) {
                $where = " AND r.new = 1 ";
            } else if ($new === false) {
                $where = " AND r.new = 0 ";
            }

            $sql = "SELECT
                    m
                FROM
                    SymbbCoreMessageBundle:Message m
                LEFT JOIN
                    m.receivers r
                WHERE
                  r.user = ?1 " . $where . "
                GROUP BY
                    m.id
                ORDER BY
                  m.date DESC";

            $query = $this->em->createQuery($sql);
            $query->setParameter(1, $receiver->getId());

            $pagination = $this->createPagination($query, $page, $limit);
            $this->setCacheData($cacheKey, $pagination);
        }

        return $pagination;
    }

    public function parseMessage(Message $message)
    {
        $text = $message->getMessage();
        $event = new ParseMessageEvent($message, (string)$text);
        $this->eventDispatcher->dispatch('symbb.core.message.manager.parse.message', $event);
        $text = $event->getText();

        return $text;
    }

    /**
     * @param Receiver $receiver
     */
    public function read(Receiver $receiver)
    {
        $receiver->setNew(false);
        $this->em->persist($receiver);
        $this->em->flush();
    }

    /**
     * @param Receiver $receiver
     */
    public function unread(Receiver $receiver)
    {
        $receiver->setNew(true);
        $this->em->persist($receiver);
        $this->em->flush();
    }

    /**
     * @param UserInterface $user
     * @return int
     */
    public function countNewMessages(UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }
        $cacheKey = "user_count_new_messages_" . $user->getId();
        $recievedNewMessages = $this->getCacheData($cacheKey);
        if ($recievedNewMessages === null) {
            $recievedNewMessages = $this->em->getRepository('SymbbCoreMessageBundle:Message\Receiver')->findBy(array('user' => $user->getId(), 'new' => true));
            $recievedNewMessages = count($recievedNewMessages);
            $this->setCacheData($cacheKey, $recievedNewMessages);
        }
        return $recievedNewMessages;
    }

    /**
     * @param UserInterface $user
     * @return int
     */
    public function countMessages(UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }
        $cacheKey = "user_count_messages_" . $user->getId();
        $recievedMessages = $this->getCacheData($cacheKey);
        if ($recievedMessages === null) {
            $recievedMessages = $this->em->getRepository('SymbbCoreMessageBundle:Message\Receiver')->findBy(array('user' => $user->getId()));
            $recievedMessages = count($recievedMessages);
            $this->setCacheData($cacheKey, $recievedMessages);
        }

        return $recievedMessages;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function remove(Message $message)
    {
        $this->em->remove($message);
        $this->em->persist();
        return true;
    }
}