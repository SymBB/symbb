<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Entity\Post;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forum_topic_post_files")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class File
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $changed;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Post", inversedBy="files")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="cascade", nullable=false)
     */
    private $post;

    /**
     * @ORM\Column(type="string", length=255, name="path", nullable=true)
     *
     * @var string $url
     */
    protected $path;

    public function getId()
    {
        return $this->getId();

    }

    public function getPath()
    {
        return $this->path;

    }

    public function setPath($image)
    {
        $this->path = $image;
        if ($this->path) {
            $this->changed = new \DateTime();
        }

    }
    
    public function setPost(\Symbb\Core\ForumBundle\Entity\Post $object)
    {
        $this->post = $object;

    }

    /**
     * @return \Symbb\Core\ForumBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;

    }
}