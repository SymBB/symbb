<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\MessageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SymBB\Core\MessageBundle\Entity\Message\Receiver;
use SymBB\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="user_messages")
 * @ORM\Entity()
 * @Vich\Uploadable
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
     */
    protected $subject;

    /**
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @ORM\OneToMany(targetEntity="\SymBB\Core\MessageBundle\Entity\Message\Receiver", mappedBy="message", cascade={"persist"})
     * @var ArrayCollection
     */
    protected $receivers;

    /**
     * @ORM\ManyToOne(targetEntity="\SymBB\Core\UserBundle\Entity\User", inversedBy="messages_sent")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $sender;

    /**
     * @ORM\Column(type="datetime")
     *
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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getReceivers()
    {
        return $this->receivers;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
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
    public function addReceiver(Receiver $receiver){
        $this->receivers->add($receiver);
    }


}