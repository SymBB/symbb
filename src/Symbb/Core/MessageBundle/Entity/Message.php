<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\MessageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symbb\Core\MessageBundle\Entity\Message\Receiver;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user_messages")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Message
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $subject;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $message;

    /**
     * @ORM\OneToMany(targetEntity="\Symbb\Core\MessageBundle\Entity\Message\Receiver", mappedBy="message", cascade={"persist"})
     * @Assert\NotBlank()
     * @Assert\Count(
     *      min = "1",
     *      minMessage = "You must specify at least one receiver"
     * )
     * @var ArrayCollection
     */
    protected $receivers;

    /**
     * @ORM\Column(name="sender_id", nullable=true, type="integer")
     * @Assert\NotBlank()
     */
    protected $senderId;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @var \DateTime $date
     */
    protected $date;

    public function __construct()
    {
        $this->receivers = new ArrayCollection();
        $this->date = new \DateTime();
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $receivers
     */
    public function setReceivers($receivers)
    {
        $this->receivers = $receivers;
    }

    /**
     * @return Receiver[]
     */
    public function getReceivers()
    {
        return $this->receivers;
    }

    /**
     * @param mixed $sender
     */
    public function setSenderId($sender)
    {
        if(is_object($sender)){
            $sender = $sender->getId();
        }
        $this->senderId = $sender;
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }


    /**
     * @param Receiver $receiver
     */
    public function addReceiver(Receiver $receiver)
    {
        $this->receivers->add($receiver);
    }


}