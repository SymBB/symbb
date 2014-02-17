<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser implements UserInterface, ParticipantInterface
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
     * @ORM\ManyToMany(targetEntity="\SymBB\Core\UserBundle\Entity\Group", cascade={"all"})
     * @ORM\JoinTable(name="user_groups",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")},
     * )
     * @var array(<"\SymBB\Core\UserBundle\Entity\GroupInterface">) 
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="\SymBB\Core\ForumBundle\Entity\Topic", mappedBy="author")
     * @var array(<"\SymBB\Core\ForumBundle\Entity\Topic">)
     */
    private $topics;

    /**
     * @ORM\OneToMany(targetEntity="\SymBB\Core\ForumBundle\Entity\Post", mappedBy="author")
     * @var array(<"\SymBB\Core\ForumBundle\Entity\Post">)
     */
    private $posts;

    /**
     * @ORM\OneToOne(targetEntity="\SymBB\Core\UserBundle\Entity\User\Data", cascade={"persist"})
     * @ORM\JoinColumn(name="data_id", referencedColumnName="id", onDelete="SET NULL")
     * @var array(<"\SymBB\Core\UserBundle\Entity\User\Data">)
     */
    private $symbbData;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $created;

    public function __construct()
    {
        parent::__construct();
        $this->topics = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->created = new \DateTime();

    }


    /**
     * 
     * @return array(<"\SymBB\Core\ForumBundle\Entity\Topic">)
     */
    public function getTopics()
    {
        return $this->topics;

    }

    /**
     * 
     * @return array(<"\SymBB\Core\ForumBundle\Entity\Post">)
     */
    public function getPosts()
    {
        return $this->posts;

    }

    /**
     * 
     * @param \SymBB\Core\UserBundle\Entity\User\Data $value
     */
    public function setSymbbData(\SymBB\Core\UserBundle\Entity\User\Data $value)
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
     * @return array(<"\SymBB\Core\UserBundle\Entity\GroupInterface">)
     */
    public function getGroups()
    {
        return $this->groups;

    }

    /**
     * 
     * @param array(<"\SymBB\Core\UserBundle\Entity\GroupInterface">) $value
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
     * 
     * @return \SymBB\Core\UserBundle\Entity\User\Data
     */
    public function getSymbbData()
    {
        $data = $this->symbbData;
        if (!is_object($data)) {
            $this->symbbData = $data = new User\Data();
        }
        return $data;

    }
}