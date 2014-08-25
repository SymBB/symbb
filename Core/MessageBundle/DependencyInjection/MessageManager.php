<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\MessageBundle\DependencyInjection;

use SymBB\Core\MessageBundle\Entity\Message;
use SymBB\Core\MessageBundle\Entity\Message\Receiver;
use SymBB\Core\MessageBundle\Event\ParseMessageEvent;
use SymBB\Core\SystemBundle\DependencyInjection\AbstractManager;
use SymBB\Core\UserBundle\Entity\UserInterface;

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
    public function find($id){
        $message = $this->em->getRepository('SymBBCoreMessageBundle:Message')->find($id);
        return $message;
    }

    /**
     * @param $subject
     * @param $messageText
     * @param $receivers
     * @param UserInterface $sender
     * @return array
     */
    public function sendMessage($subject, $messageText, $receivers, UserInterface $sender = null){

        $errors = array();
        //todo event beforSend

        if(!$sender){
            $sender = $this->getUser();
        }

        if($sender->getSymbbType() !== 'user'){
            $errors[] = self::ERROR_NOT_ALLOWED;
        }

        if(empty($subject)){
            $errors[] = self::ERROR_SUBJECT_EMPTY;
        }
        if(empty($messageText)){
            $errors[] = self::ERROR_MESSAGE_EMPTY;
        }

        $message = new Message();
        $message->setSubject($subject);
        $message->setMessage($messageText);
        $message->setSender($sender);
        foreach($receivers as $receiver){
            if($receiver instanceof UserInterface){
                $receiverObject = new Receiver();
                $receiverObject->setUser($receiver);
                $receiverObject->setMessage($message);
                $message->addReceiver($receiverObject);
            } else {
                $errors[] = self::ERROR_RECEIVER_NOT_FOUND;
            }
        }

        if(empty($errors)){
            // "send" means in first step saving into database, notify user if option is actived,...
            $this->em->persist($message);
            $this->em->flush();
        }

        //todo event afterSend
        return $errors;
    }

    /**
     * @param UserInterface $sender
     * @param int $page
     * @param int $limit
     * @return Message[]
     */
    public function findSentMessages(UserInterface $sender = null, $page = 1, $limit = 20){

        if(!$sender){
            $sender = $this->getUser();
        }

        $sql = "SELECT
                    m
                FROM
                    SymBBCoreMessageBundle:Message m
                WHERE
                  m.sender = ?1
                ORDER BY
                  m.date DESC";

        $query = $this->em->createQuery($sql);
        $query->setParameter(1, $sender->getId());

        $pagination = $this->createPagination($query, $page, $limit);

        return $pagination;
    }

    /**
     * @param UserInterface $receiver
     * @param int $page
     * @param int $limit
     * @return Message[]
     */
    public function findReceivedMessages(UserInterface $receiver = null, $page = 1, $limit = 20){

        if(!$receiver){
            $receiver = $this->getUser();
        }

        $sql = "SELECT
                    m
                FROM
                    SymBBCoreMessageBundle:Message m
                LEFT JOIN
                    m.receivers r
                WHERE
                  r.user = ?1
                ORDER BY
                  m.date DESC";

        $query = $this->em->createQuery($sql);
        $query->setParameter(1, $receiver->getId());

        $pagination = $this->createPagination($query, $page, $limit);

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
    public function read(Receiver $receiver){
        $receiver->setNew(false);
        $this->em->persist($receiver);
        $this->em->flush();
    }
}