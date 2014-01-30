<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="posts")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id", onDelete="cascade")
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Core\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="NO ACTION")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="SymBB\Core\ForumBundle\Entity\Post\Flag", mappedBy="post")
     */
    private $flags;

    /**
     * @ORM\OneToMany(targetEntity="SymBB\Core\ForumBundle\Entity\Post\File", mappedBy="post")
     */
    private $files;

    public function __construct()
    {
        $this->likes    = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
        $this->files    = new ArrayCollection();
        $this->flags    = new ArrayCollection();

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

    public function getFlags()
    {
        return $this->flags;

    }

    public function setFiles($files)
    {
        $this->files = $files;

    }

    public function getFiles()
    {
        return $this->files;

    }

    public function addFile(\SymBB\Core\ForumBundle\Entity\Post\File $file)
    {
        $this->files->add($file);

    }
    
    public function removeFile($key)
    {
       $this->files->remove($key);
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

    public static function createNew(Topic $topic, \SymBB\Core\UserBundle\Entity\UserInterface $user)
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
}