<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Entity\Topic;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forum_topic_tags")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Tag
{

    /**
     * @ORM\Column(type="string", unique=true, length=20)
     * @ORM\Id()
     */
    private $id = '';


    /**
     * @ORM\ManyToMany(targetEntity="Symbb\Core\ForumBundle\Entity\Topic", mappedBy="tags")
     */
    private $topics;

    /**
     * @ORM\Column(type="string")
     */
    private $name = "";

    /**
     * @ORM\Column(type="integer")
     */
    private $priority = 0;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    /**
     * @param integer $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return \Symbb\Core\ForumBundle\Entity\Topic[]
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * @param \Symbb\Core\ForumBundle\Entity\Topic[]
     */
    public function setTopics($value)
    {
        $this->topics = $value;
    }

    /**
     * @param \Symbb\Core\ForumBundle\Entity\Topic
     */
    public function addTopic($value)
    {
        $this->topics->add($value);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setPosition($value){
        $this->priority = $value;
    }

    public function __toString(){
        return $this->getName();
    }

}