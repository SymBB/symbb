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
use SymBB\Core\SystemBundle\DependencyInjection\AbstractManager;
use SymBB\Core\UserBundle\DependencyInjection\UserManager;
use SymBB\Core\UserBundle\Entity\UserInterface;
use \Doctrine\ORM\EntityManager ;
use \Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;

class MessageManager extends AbstractManager
{


    public function sendMessage(Message $message, UserInterface $sender, $receivers){

        //todo event beforSend

        // "send" means in first step saving into database, notify user if option is actived,...

        //todo event afterSend

    }

    public function findSentMessages(UserInterface $sender, $page = 1, $limit = 20){

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

    public function findReceivedMessages(UserInterface $receiver, $page = 1, $limit = 20){

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
}