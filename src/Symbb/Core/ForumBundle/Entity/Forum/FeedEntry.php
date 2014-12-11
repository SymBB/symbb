<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Entity\Forum;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forum_feed_entries")
 * @ORM\Entity()
 */
class FeedEntry
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title = "";

    /**
     * @ORM\Column(type="string")
     */
    protected $link = "";

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Forum\Feed", inversedBy="entries")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $feed;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Forum", inversedBy="feedEntries")
     * @ORM\JoinColumn(name="forum_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $forum;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct(){
        $this->created = new \DateTime();
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return boolean
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * @param boolean $new
     */
    public function setNew($new)
    {
        $this->new = $new;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param mixed $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * @param mixed $forum
     */
    public function setForum($forum)
    {
        $this->forum = $forum;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


}