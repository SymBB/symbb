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
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")},
     * )
     * @var \Symbb\Core\UserBundle\Entity\GroupInterface[]
     */
    protected $groups;

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

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->created = new \DateTime();
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
        if (is_array($value)) {
            $value = new ArrayCollection($value);
        }
        $this->groups = $value;
    }

    /**
     * @param GroupInterface $group
     */
    public function addGroup(\FOS\UserBundle\Model\GroupInterface $group)
    {
        $this->groups->add($group);
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
     * @return \DateTime
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param string $pw
     */
    public function setPlainPassword($pw)
    {
        if (!empty($pw)) {
            parent::setPlainPassword($pw);
            $this->changed = new \DateTime();
        }
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->enabled === 0 || $this->enabled === false) {
            return false;
        }
        return true;
    }

    /**
     *
     */
    public function disable()
    {
        $this->enabled = 0;
    }

    /**
     *
     */
    public function enable()
    {
        $this->enabled = 1;
    }
}