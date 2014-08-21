<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\MessageBundle\Entity\Message;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="user_message_receivers")
 * @ORM\Entity()
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class Receiver
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\SymBB\Core\MessageBundle\Entity\Message", inversedBy="receivers", cascade={"persist", "remove"})
     * @var ArrayCollection
     */
    protected $message;

    /**
     * @ORM\ManyToOne(targetEntity="\SymBB\Core\UserBundle\Entity\User", inversedBy="messages_receive")
     */
    protected $user;

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
     * @param \Doctrine\Common\Collections\ArrayCollection $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }



}