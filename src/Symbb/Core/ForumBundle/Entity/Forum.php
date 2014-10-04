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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="forums")
 * @ORM\Entity()
 * @Vich\Uploadable
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
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="symbb_forum_image", fileNameProperty="imageName")
     *
     * @var File $image
     */
    protected $image;

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
     * @ORM\OneToMany(targetEntity="Forum", mappedBy="parent", cascade={"persist", "remove"})
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
     * @ORM\OneToMany(targetEntity="Topic", mappedBy="forum")
     * @ORM\OrderBy({"changed" = "ASC", "created" = "ASC"})
     * @var ArrayCollection 
     */
    protected $topics;

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

    protected $topicCount = null;

    protected $postCount = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->topics = new ArrayCollection();

    }


    public function getLink()
    {
        return $this->link;

    }

    public function setLink($value)
    {
        $this->link = $value;

    }

    public function getCountLinkCalls()
    {
        return $this->countLinkCalls;

    }

    public function setCountLinkCalls($value)
    {
        $this->countLinkCalls = $value;

    }

    public function getDescription()
    {
        return $this->description;

    }

    public function setDescription($value)
    {
        $this->description = $value;

    }

    public function isActive()
    {
        return $this->active;

    }

    public function setActive($value)
    {
        $this->active = $value;

    }

    public function getType()
    {
        return $this->type;

    }

    public function setType($value)
    {
        $this->type = $value;

    }

    public function hasShowSubForumList()
    {
        return $this->showSubForumList;

    }

    public function setShowSubForumList($value)
    {
        $this->showSubForumList = $value;

    }

    public function getEntriesPerPage()
    {
        return $this->entriesPerPage;

    }

    public function setEntriesPerPage($value)
    {
        $this->entriesPerPage = $value;

    }

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

    public function getChildren()
    {
        return $this->children;

    }
    
    public function hasChildren(){
        if($this->getChildren()->count() > 0){
            return true;
        }
        return false;
    }

    public function setChildren($children){
        $this->children = $children;
    }

    public function getTopics()
    {
        return $this->topics;

    }

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

    public function hasTopics()
    {
        $topics = $this->getTopics();
        if ($topics->count() > 0) {
            return true;
        }
        return false;

    }

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

    public function getImage()
    {
        return $this->image;

    }

    public function getImageName()
    {
        return $this->imageName;

    }

    public function setImageName($name)
    {
        $this->imageName = $name;

    }
}