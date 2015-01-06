<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symbb\Core\ForumBundle\Entity\Forum\Feed;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="forums")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Forum extends \Symbb\Core\SystemBundle\Entity\Base\CrudAbstract
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, name="image_name", nullable=true)
     *
     * @var string $imageName
     */
    protected $imageName;

    /**
     * @ORM\Column(type="string", length=10))
     */
    protected $type = 'category';

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $link;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $countLinkCalls = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="Forum", mappedBy="parent", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     * @var ArrayCollection
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Forum", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Topic", mappedBy="forum", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"changed" = "ASC", "created" = "ASC"})
     * @var ArrayCollection
     */
    protected $topics;

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\ForumBundle\Entity\Forum\Feed", mappedBy="forum", orphanRemoval=true, cascade={"persist"}, fetch="EXTRA_LAZY")
     * @var ArrayCollection
     */
    protected $feeds;

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\ForumBundle\Entity\Forum\FeedEntry", mappedBy="forum", orphanRemoval=true, cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     * @var ArrayCollection
     */
    protected $feedEntries;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $active = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $showSubForumList = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $entriesPerPage = 20;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position = 999;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var null
     */
    protected $topicCount = null;

    /**
     * @var null
     */
    protected $postCount = null;

    /**
     *
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->topics = new ArrayCollection();
        $this->feeds = new ArrayCollection();

    }


    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;

    }

    /**
     * @param $value
     */
    public function setLink($value)
    {
        $this->link = $value;

    }

    /**
     * @return bool
     */
    public function getCountLinkCalls()
    {
        return $this->countLinkCalls;

    }

    /**
     * @param $value
     */
    public function setCountLinkCalls($value)
    {
        $this->countLinkCalls = $value;

    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;

    }

    /**
     * @param $value
     */
    public function setDescription($value)
    {
        $this->description = $value;

    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;

    }

    /**
     * @param $value
     */
    public function setActive($value)
    {
        $this->active = $value;

    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;

    }

    /**
     * @param $value
     */
    public function setType($value)
    {
        $this->type = $value;

    }

    /**
     * @return bool
     */
    public function hasShowSubForumList()
    {
        return $this->showSubForumList;

    }

    /**
     * @param $value
     */
    public function setShowSubForumList($value)
    {
        $this->showSubForumList = $value;

    }

    /**
     * @return int
     */
    public function getEntriesPerPage()
    {
        return $this->entriesPerPage;

    }

    /**
     * @param $value
     */
    public function setEntriesPerPage($value)
    {
        $this->entriesPerPage = $value;

    }

    /**
     * @param $object
     */
    public function setParent($object)
    {
        $this->parent = $object;

    }

    /**
     * @return Forum
     */
    public function getParent()
    {
        return $this->parent;

    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;

    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        if ($this->getChildren()->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return ArrayCollection
     */
    public function getTopics()
    {
        return $this->topics;

    }

    /**
     * @return int|null
     */
    public function getTopicCount()
    {
        $count = $this->topicCount;
        if ($count === null) {
            $topics = $this->getTopics();
            $count = count($topics);
            $childs = $this->getChildren();
            foreach ($childs as $child) {
                $count += $child->getTopicCount();
            }
            $this->topicCount = $count;
        }

        return $count;

    }

    /**
     * @return int|null
     */
    public function getPostCount()
    {
        $count = $this->postCount;
        if ($count === null) {
            $topics = $this->getTopics();
            $count = 0;
            foreach ($topics as $topic) {
                $posts = $topic->getPosts();
                $count += count($posts);
                $count = $count - 1; // topic post
            }
            $childs = $this->getChildren();
            foreach ($childs as $child) {
                $count += $child->getPostCount();
            }
            $this->postCount = $count;
        }

        return $count;

    }

    /**
     * @return null
     */
    public function getLastPost()
    {
        $lastPost = null;
        $topics = $this->getTopics();
        foreach ($topics as $topic) {
            $currLastPost = $topic->getPosts()->last();
            if (
                \is_object($currLastPost) &&
                (
                    !$lastPost ||
                    $currLastPost->getChanged() > $lastPost->getChanged()
                )
            ) {
                $lastPost = $currLastPost;
            }
        }
        $childs = $this->getChildren();
        foreach ($childs as $child) {
            $lastChildPost = $child->getLastPost();
            if (
                is_object($lastChildPost) &&
                (
                    !$lastPost ||
                    $lastChildPost->getChanged() > $lastPost->getChanged()
                )
            ) {
                $lastPost = $lastChildPost;
            }
        }

        return $lastPost;

    }

    /**
     * @return bool
     */
    public function hasTopics()
    {
        $topics = $this->getTopics();
        if ($topics->count() > 0) {
            return true;
        }

        return false;

    }

    /**
     * @param $image
     */
    public function setImage($image)
    {
        $this->image = $image;

        if ($this->image) {
            $this->updatedAt = new \DateTime('now');
        }

    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime('now');

    }

    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;

    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;

    }

    /**
     * @param $name
     */
    public function setImageName($name)
    {
        $this->imageName = $name;

    }

    /**
     * @return array
     */
    public function getAvailableTypes()
    {
        return array(
            'forum',
            'category',
            'link',
            'rss'
        );
    }



    /**
     * @return ArrayCollection
     */
    public function getFeeds()
    {
        return $this->feeds;

    }

    /**
     *
     */
    public function removeFeeds(){
        $this->feeds->clear();
    }

    /**
     * @param Feed $feed
     */
    public function addFeed(Feed $feed){
        $feed->setForum($this);
        $this->feeds->add($feed);
    }

    /**
     * @param ArrayCollection $feeds
     */
    public function setFeeds(ArrayCollection $feeds){
        $this->feeds->clear();
        $this->feeds = $feeds;
    }
}