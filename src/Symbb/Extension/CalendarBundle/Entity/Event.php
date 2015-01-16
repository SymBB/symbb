<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forum_topic_post_calendar_events")
 * @ORM\Entity()
 */
class Event
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", unique=true, onDelete="cascade")
     */
    private $post;

    /**
     * @ORM\ManyToMany(targetEntity="Symbb\Core\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="forum_topic_post_calendar_event_groups",
     *      joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    private $groups;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    public function getId()
    {
        return $this->id;

    }

    public function setId($value)
    {
        $this->id = $value;

    }

    public function setPost($object)
    {
        $this->post = $object;

    }

    /**
     *
     * @return \Symbb\Core\ForumBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;

    }

    public function setStartDate(\DateTime $value)
    {
        $this->startDate = $value;

    }

    public function setEndDate(\DateTime $value)
    {
        $this->endDate = $value;

    }

    /**
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;

    }

    /**
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;

    }

    /**
     * @return array(<"\Symbb\Core\UserBundle\Entity\GroupInterface">)
     */
    public function getGroups()
    {
        return $this->groups;

    }

    /**
     *
     * @param array(<"\Symbb\Core\UserBundle\Entity\GroupInterface">) $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;

    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;

    }

    /**
     *
     * @param string $title
     */
    public function setName($title)
    {
        $this->name = $title;

    }
}