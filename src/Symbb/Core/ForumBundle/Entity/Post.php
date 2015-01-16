<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symbb\Core\ForumBundle\Entity\Post\History;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="forum_topic_posts")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     */
    private $changed;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="posts", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id", onDelete="cascade", nullable=false)
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="NO ACTION", nullable=false)
     */
    private $author;


    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\ForumBundle\Entity\Post\File", orphanRemoval=true, mappedBy="post", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    private $files;


    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\ForumBundle\Entity\Post\History", orphanRemoval=true, mappedBy="post", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"changed" = "DESC"})
     */
    private $history;


    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->history = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($value)
    {
        $this->text = $value;
    }

    public function setTopic($object)
    {
        $this->topic = $object;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function addFile(\Symbb\Core\ForumBundle\Entity\Post\File $file)
    {
        if ($file->getPath()) {
            $this->files->add($file);
            $file->setPost($this);
        }
    }

    public function removeFile($file)
    {
        $this->files->removeElement($file);
    }

    /**
     *
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    public function setAuthor($object)
    {
        $this->author = $object;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setChangedValue()
    {
        $this->changed = new \DateTime();
    }

    public function getSeoName()
    {
        $name = $this->getName();
        $name = preg_replace('/\W+/', '-', $name);
        $name = strtolower(trim($name, '-'));
        return $name;
    }

    public static function createNew(Topic $topic, \Symbb\Core\UserBundle\Entity\UserInterface $user)
    {
        $post = new self();
        $post->setTopic($topic);
        $post->setAuthor($user);
        $post->setName($topic->getName());
        return $post;
    }

    public function getParent()
    {
        return $this->getTopic();
    }

    /**
     * @param History[] $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }

    /**
     * @return History[]
     */
    public function getHistory()
    {
        return $this->history;
    }

    public function addHistory($history)
    {
        $this->history->add($history);
    }


}