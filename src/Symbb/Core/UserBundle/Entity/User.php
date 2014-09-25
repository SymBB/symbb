<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser implements UserInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="type", type="string", length=10))
     */
    protected $symbbType = 'user';

    /**
     * @ORM\ManyToMany(targetEntity="\Symbb\Core\UserBundle\Entity\Group", cascade={"persist"})
     * @ORM\JoinTable(name="user_groups",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     * )
     * @var array(<"\Symbb\Core\UserBundle\Entity\GroupInterface">)
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="\Symbb\Core\ForumBundle\Entity\Topic", mappedBy="author")
     * @var array(<"\Symbb\Core\ForumBundle\Entity\Topic">)
     */
    private $topics;

    /**
     * @ORM\OneToMany(targetEntity="\Symbb\Core\ForumBundle\Entity\Post", mappedBy="author")
     * @var array(<"\Symbb\Core\ForumBundle\Entity\Post">)
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="\Symbb\Core\MessageBundle\Entity\Message\Receiver", mappedBy="user", cascade={"persist"})
     * @var array(<"\Symbb\Core\MessageBundle\Entity\Message\Receiver">)
     */
    private $messages_receive;

    /**
     * @ORM\OneToMany(targetEntity="\Symbb\Core\MessageBundle\Entity\Message", mappedBy="sender")
     * @var array(<"\Symbb\Core\MessageBundle\Entity\Message">)
     */
    private $messages_sent;



    /**
     * @ORM\OneToOne(targetEntity="\Symbb\Core\UserBundle\Entity\User\Data", cascade={"persist"})
     * @ORM\JoinColumn(name="data_id", referencedColumnName="id", onDelete="SET NULL")
     * @var array(<"\Symbb\Core\UserBundle\Entity\User\Data">)
     */
    private $symbbData;

    /**
     * @ORM\OneToMany(targetEntity="\Symbb\Core\UserBundle\Entity\User\FieldValue", cascade={"persist"}, mappedBy="user")
     * @var array(<"\Symbb\Core\UserBundle\Entity\User\Field">)
     */
    private $symbbFieldValues;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $changed;

    public function __construct()
    {
        parent::__construct();
        $this->topics = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->created = new \DateTime();
        $this->symbbFieldValues = new ArrayCollection();
    }

    /**
     * 
     * @return array(<"\Symbb\Core\ForumBundle\Entity\Topic">)
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * 
     * @return array(<"\Symbb\Core\ForumBundle\Entity\Post">)
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * 
     * @param \Symbb\Core\UserBundle\Entity\User\Data $value
     */
    public function setSymbbData(\Symbb\Core\UserBundle\Entity\User\Data $value)
    {
        $this->symbbData = $value;
    }

    /**
     * 
     * @return string
     */
    public function getEmail()
    {
        return parent::getEmail();
    }

    /**
     * 
     * @return integer
     */
    public function getId()
    {
        return parent::getId();
    }

    /**
     * 
     * @return string
     */
    public function getUsername()
    {
        return parent::getUsername();
    }

    /**
     * 
     * @return array(<"\Symbb\Core\UserBundle\Entity\GroupInterface">)
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * 
     * @param array(<"\Symbb\Core\UserBundle\Entity\GroupInterface">) $value
     */
    public function setGroups($value)
    {
        $this->groups = $value;
    }

    /**
     * 
     * @param string $value
     */
    public function setSymbbType($value)
    {
        $this->symbbType = $value;
    }

    /**
     * 
     * @return string
     */
    public function getSymbbType()
    {
        return $this->symbbType;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setChangedValue()
    {
        $this->changed = new \DateTime();
    }

    /**
     * 
     * @return \Symbb\Core\UserBundle\Entity\User\Data
     */
    public function getSymbbData()
    {
        $data = $this->symbbData;
        if (!is_object($data)) {
            $this->symbbData = $data = new User\Data();
        }
        return $data;
    }

    public function setPlainPassword($pw)
    {
        if(!empty($pw)){
            parent::setPlainPassword($pw);
            $this->changed = new \DateTime();
        }
    }

    public function isEnabled()
    {
        if ($this->enabled === 0 || $this->enabled === false) {
            return false;
        }
        return true;
    }

    public function disable()
    {
        $this->enabled = 0;
    }

    public function enable()
    {
        $this->enabled = 1;
    }

    public function getFieldValues()
    {
        return $this->symbbFieldValues;
    }

    /**
     * @param Field $field
     * @return null|User\FieldValue
     */
    public function getFieldValue(\Symbb\Core\UserBundle\Entity\Field $field)
    {
        $values = $this->getFieldValues();
        $found = null;
        foreach ($values as $value) {
            if ($value->getField()->getId() === $field->getId()) {
                $found = $value;
            }
        }
        if (!$found || !is_object($found)) {
            $found = new \Symbb\Core\UserBundle\Entity\User\FieldValue();
            $found->setField($field);
            $found->setUser($this);
        }

        return $found;
    }
}